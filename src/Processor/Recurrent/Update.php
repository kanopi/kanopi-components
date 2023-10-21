<?php

namespace Kanopi\Components\Processor\Recurrent;

use Exception;
use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Process\IIndexedProcessStatistics;
use Kanopi\Components\Model\Data\Process\IndexedProcessStatistics;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\ImportStreamException;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Processor\DryRunProcessor;
use Kanopi\Components\Processor\IDryRunProcessor;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * Base pattern for Updatable processing
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Maintains process state statistics for all processed entities
 *
 * @package kanopi/components
 */
abstract class Update implements IDryRunProcessor {
	use DryRunProcessor;

	// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- No direct output, only exception messaging
	/**
	 * External data import source service
	 *
	 * @var IExternalStreamReader
	 */
	protected IExternalStreamReader $externalService;
	/**
	 * @var ILogger
	 */
	protected ILogger $logger;
	/**
	 * Target data store service
	 *
	 * @var IIndexedEntityWriter
	 */
	protected IIndexedEntityWriter $systemService;
	/**
	 * Statistics for the current process
	 *
	 * @var IIndexedProcessStatistics
	 */
	protected IIndexedProcessStatistics $processStatistics;
	/**
	 * Whether to overwrite existing content during updates
	 *
	 * @var bool
	 */
	protected bool $overwriteContent = false;
	/**
	 * Whether to stop processing the entire record set on any entity errors
	 *
	 * @var bool
	 */
	protected bool $stopOnError = false;

	/**
	 * @param ILogger               $_logger           Logging service
	 * @param IExternalStreamReader $_external_service External source data service
	 * @param IIndexedEntityWriter  $_system_service   Internal target data service
	 */
	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service
	) {
		$this->processStatistics = new IndexedProcessStatistics();

		$this->logger          = $_logger;
		$this->externalService = $_external_service;
		$this->systemService   = $_system_service;
	}

	/**
	 * {@inheritDoc}
	 */
	public function changeOverwriteStatus( bool $_enableState ): void {
		$this->overwriteContent = $_enableState;
	}

	/**
	 * {@inheritDoc}
	 */
	public function changeStopOnError( bool $_enableState ): void {
		$this->stopOnError = $_enableState;
	}

	/**
	 * {@inheritDoc}
	 * @throws ImportStreamException Failure to process import stream
	 */
	public function process( string $_input_stream_uri ): void {
		try {
			$processStartTime = hrtime( true );

			$streamProperties = $this->externalService()->readStream(
				$_input_stream_uri,
				$this->incomingEntityTransformer()
			);

			$this->preProcessValidationEvents( $streamProperties );

			$this->preProcessBanner( $streamProperties );
			if ( false === $this->isStreamProcessValid( $streamProperties ) ) {
				return;
			}

			$this->preProcessEvents();
			$this->mainProcessingEvent();
			$this->postProcessingEvents();
			$this->postProcessingBanner();

			$processEndTime = hrtime( true );
			$elapsedTime    = round( ( $processEndTime - $processStartTime ) / 1e+9, 1 );
			$this->logger()->info( "Process run took $elapsedTime seconds." );
		} catch ( Exception $exception ) {
			throw new ImportStreamException(
				"Record set unavailable | {$exception->getMessage()}"
			);
		}
	}

	/**
	 * External data import source service
	 *
	 * @return IExternalStreamReader
	 */
	protected function externalService(): IExternalStreamReader {
		return $this->externalService;
	}

	/**
	 * Entity transformer for all incoming data
	 *
	 * @return IEntitySet
	 */
	abstract protected function incomingEntityTransformer(): IEntitySet;

	/**
	 * Events before process validation
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 * @throws SetReaderException Could not validate input set
	 *
	 */
	abstract protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void;

	/**
	 * Logging banner to show before processing begins
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 */
	protected function preProcessBanner( IStreamProperties $_streamProperties ): void {
		$this->logger()->table(
			$this->preProcessingBannerHeader(),
			[
				$this->preProcessingBannerDataRow( $_streamProperties ),
			]
		);
	}

	/**
	 * Logging interface
	 *
	 * @return ILogger
	 */
	protected function logger(): ILogger {
		return $this->logger;
	}

	/**
	 * Override to add more banner columns
	 *    - Each entry is an index in the data row
	 *
	 * @return array
	 */
	protected function preProcessingBannerHeader(): array {
		return [
			'Stream Last Modified Date',
			'Stream Length',
			'Stream Uri',
		];
	}

	/**
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return string[]
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return [
			'Stream Last Modified Date' => gmdate( 'm-d-Y H:i:s', $_streamProperties->lastModifiedTimestamp() ),
			'Stream Length'             => $_streamProperties->length(),
			'Stream Uri'                => $_streamProperties->uri(),
		];
	}

	/**
	 * Stream validity check
	 *    - Return false to stop processing
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return bool
	 */
	abstract protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool;

	/**
	 * Events to execute before processing
	 *    - Override this in extended classes for additional events
	 *
	 * @return void
	 */
	protected function preProcessEvents(): void {
		if ( $this->isDryRunEnabled() ) {
			$this->logger()->info( 'Dry run is enabled for this process' );
		}
	}

	/**
	 * Main event to process the entire entity set
	 *
	 * @throws SetWriterException Unable to write to one or more system entities
	 */
	protected function mainProcessingEvent(): void {
		foreach ( $this->readExternalEntities() as $externalEntity ) {
			$this->processSystemEntity( $externalEntity );
			$this->postSystemEntityProcessedEvent( $externalEntity );
		}
	}

	/**
	 * Read the set of external entities, converted to the common entity type, to process
	 *    - Override this in extended classes batching, etc
	 *
	 * @return iterable
	 */
	protected function readExternalEntities(): iterable {
		$entities = $this->externalService()->read();
		$this->processStatistics->incomingTotal( $entities->count() );
		return $entities;
	}

	/**
	 * Process the supplied system entity
	 *
	 * @param IIndexedEntity $_systemEntity Current system entity
	 *
	 * @return IIndexedEntity Final updated entity
	 * @throws SetWriterException Unable to write to one or more system entities
	 *
	 */
	protected function processSystemEntity( IIndexedEntity $_systemEntity ): IIndexedEntity {
		try {
			$systemEntity    = $this->readSystemEntityByIdentifier( $_systemEntity->uniqueIdentifier() );
			$hasSystemEntity = ! empty( $systemEntity );

			return $hasSystemEntity
				? $this->processExistingSystemEntity( $_systemEntity, $systemEntity )
				: $this->processNewSystemEntity( $_systemEntity );
		} catch ( SetReaderException | SetWriterException $exception ) {
			$message = sprintf(
				'Error processing entity with UID %s: %s',
				$_systemEntity->uniqueIdentifier(),
				$exception->getMessage()
			);

			$this->logger()->error( $message );

			// When stop on error is set, rethrow the error to stop processing
			if ( $this->stopOnError ) {
				throw new SetWriterException( $message );
			}

			return $_systemEntity;
		}
	}

	/**
	 * Read an internal system entity using a cross-system unique identifier
	 *
	 * @param string $_identifier Entity cross-system unique identifier
	 *
	 * @return IIndexedEntity|null
	 * @throws SetReaderException Unable to read the system entity
	 */
	protected function readSystemEntityByIdentifier( string $_identifier ): ?IIndexedEntity {
		return $this->systemService()->readByUniqueIdentifier( $_identifier );
	}

	/**
	 * Compares entity version(), updates if matched, otherwise skips
	 *
	 * @param IIndexedEntity $_incoming Incoming, external entity model
	 * @param IIndexedEntity $_existing Current, internal entity model
	 *
	 * @return IIndexedEntity
	 * @throws SetWriterException Unable to update
	 */
	protected function processExistingSystemEntity(
		IIndexedEntity $_incoming,
		IIndexedEntity $_existing
	): IIndexedEntity {
		$_incoming->updateIndexIdentifier( $_existing->indexIdentifier() );

		if ( $this->shouldEntityUpdate( $_existing, $_incoming ) ) {
			if ( false === $this->isDryRunEnabled() ) {
				$this->systemService()->update( $_incoming );
			}

			$this->processStatistics->updated( $_incoming->indexIdentifier() );
		} else {
			$this->processStatistics->skipped( $_incoming->indexIdentifier() );
		}

		return $_incoming;
	}

	/**
	 * Compares the existing System entity against the incoming entity to determine if the entity should update
	 *    - By default, compares the two versions and updates if they are different (!==)
	 *  - If overwriteContent is set, it will return true
	 *
	 * @param IIndexedEntity $_existing Current, internal entity model
	 * @param IIndexedEntity $_incoming Incoming, external entity model
	 *
	 * @return bool
	 */
	protected function shouldEntityUpdate( IIndexedEntity $_existing, IIndexedEntity $_incoming ): bool {
		return $this->overwriteContent || $_incoming->version() !== $_existing->version();
	}

	/**
	 * Target data store service
	 *
	 * @return IIndexedEntityWriter
	 */
	protected function systemService(): IIndexedEntityWriter {
		return $this->systemService;
	}

	/**
	 * Process creating a new system entity
	 *
	 * @param IIndexedEntity $_incoming Incoming, external entity model
	 *
	 * @return IIndexedEntity
	 * @throws SetWriterException Unable to create/add new system entity
	 */
	protected function processNewSystemEntity( IIndexedEntity $_incoming ): IIndexedEntity {
		if ( false === $this->isDryRunEnabled() ) {
			$_incoming = $this->systemService()->create( $_incoming );
		}

		$this->processStatistics->created( $_incoming->indexIdentifier() );
		return $_incoming;
	}

	/**
	 * Events to execute after a system entity is processed
	 *
	 * @param IIndexedEntity $_entity Post-processed system entity
	 *
	 * @return void
	 */
	abstract protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void;

	/**
	 * Events to execute after all system entities are processed
	 *
	 * @return void
	 */
	abstract protected function postProcessingEvents(): void;

	/**
	 * Logging banner to show after all system entities are processed
	 *
	 * @return void
	 */
	protected function postProcessingBanner(): void {
		$this->logger()->table(
			$this->postProcessingBannerHeader(),
			[
				$this->postProcessingBannerDataRow(),
			]
		);
	}

	/**
	 * Override to add more banner columns
	 *    - Each entry is an index in the data row
	 *
	 * @return string[]
	 */
	protected function postProcessingBannerHeader(): array {
		return [
			'Incoming',
			'Created',
			'Updated',
			'Skipped',
			'Processed',
		];
	}

	/**
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @return array
	 */
	protected function postProcessingBannerDataRow(): array {
		return [
			'Incoming'  => $this->processStatistics->incomingTotalAmount(),
			'Created'   => $this->processStatistics->createdAmount(),
			'Updated'   => $this->processStatistics->updatedAmount(),
			'Skipped'   => $this->processStatistics->skippedAmount(),
			'Processed' => $this->processStatistics->processedTotalAmount(),
		];
	}
}
