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

abstract class Update implements IDryRunProcessor {
	use DryRunProcessor;

	/**
	 * External CSV data import source service
	 *
	 * @var IExternalStreamReader
	 */
	protected IExternalStreamReader $_externalService;

	/**
	 * @var ILogger
	 */
	protected ILogger $_logger;

	/**
	 * Target data store service
	 *
	 * @var IIndexedEntityWriter
	 */
	protected IIndexedEntityWriter $_systemService;

	/**
	 * Statistics for the current process
	 *
	 * @var IIndexedProcessStatistics
	 */
	protected IIndexedProcessStatistics $_processStatistics;

	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service
	) {
		$this->_processStatistics = new IndexedProcessStatistics();

		$this->_logger          = $_logger;
		$this->_externalService = $_external_service;
		$this->_systemService   = $_system_service;
	}

	/**
	 * Entity transformer for all incoming data
	 *
	 * @return IEntitySet
	 */
	abstract protected function incomingEntityTransformer(): IEntitySet;

	/**
	 * Stream validity check
	 *    - Return false to stop processing
	 *
	 * @param IStreamProperties $_streamProperties
	 *
	 * @return bool
	 */
	abstract protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool;

	/**
	 * Logging banner to show after all system entities are processed
	 *
	 * @return void
	 */
	protected function postProcessingBanner(): void {
		$this->_logger->table(
			$this->postProcessingBannerHeader(),
			[
				$this->postProcessingBannerDataRow()
			]
		);
	}

	/**
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @return array
	 */
	protected function postProcessingBannerDataRow(): array {
		return [
			'Incoming'  => $this->_processStatistics->incomingTotalAmount(),
			'Created'   => $this->_processStatistics->createdAmount(),
			'Updated'   => $this->_processStatistics->updatedAmount(),
			'Skipped'   => $this->_processStatistics->skippedAmount(),
			'Processed' => $this->_processStatistics->processedTotalAmount(),
		];
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
	 * Events to execute after all system entities are processed
	 *
	 * @return void
	 */
	abstract protected function postProcessingEvents(): void;

	/**
	 * Events to execute after a system entity is processed
	 *
	 * @param IIndexedEntity $_entity
	 *
	 * @return void
	 */
	abstract protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void;

	/**
	 * Logging banner to show before processing begins
	 *
	 * @param IStreamProperties $_streamProperties
	 *
	 * @return void
	 */
	protected function preProcessBanner( IStreamProperties $_streamProperties ): void {
		$this->_logger->table(
			$this->preProcessingBannerHeader(),
			[
				$this->preProcessingBannerDataRow( $_streamProperties )
			]
		);
	}

	/**
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @param IStreamProperties $_streamProperties
	 *
	 * @return string[]
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return [
			'Stream Last Modified Date' => gmdate( "m-d-Y H:i:s", $_streamProperties->lastModifiedTimestamp() ),
			'Stream Length'             => $_streamProperties->length(),
			'Stream Uri'                => $_streamProperties->uri(),
		];
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
	 * Events to execute before processing
	 *    - Override this in extended classes for additional events
	 *
	 *
	 * @return void
	 */
	protected function preProcessEvents(): void {
		if ( $this->isDryRunEnabled() ) {
			$this->_logger->info( "Dry run is enabled for this process" );
		}
	}

	/**
	 * Events before process validation
	 *
	 * @param IStreamProperties $_streamProperties
	 *
	 * @throws SetReaderException
	 *
	 * @return void
	 */
	abstract protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void;

	/**
	 * Compares entity version(), updates if matched, otherwise skips
	 *
	 * @param IIndexedEntity $_incoming
	 * @param IIndexedEntity $_existing
	 *
	 * @throws SetWriterException
	 * @return IIndexedEntity
	 */
	protected function processExistingSystemEntity(
		IIndexedEntity $_incoming,
		IIndexedEntity $_existing
	): IIndexedEntity {
		$_incoming->updateIndexIdentifier( $_existing->indexIdentifier() );

		if ( $_incoming->version() !== $_existing->version() ) {
			if ( false === $this->isDryRunEnabled() ) {
				$this->_systemService->update( $_incoming );
			}
			$this->_processStatistics->updated( $_incoming->indexIdentifier() );
		}
		else {
			$this->_processStatistics->skipped( $_incoming->indexIdentifier() );
		}

		return $_incoming;
	}

	/**
	 * Process creating a new system entity
	 *
	 * @param IIndexedEntity $_incoming
	 *
	 * @throws SetWriterException
	 * @return IIndexedEntity
	 */
	protected function processNewSystemEntity( IIndexedEntity $_incoming ): IIndexedEntity {
		if ( false === $this->isDryRunEnabled() ) {
			$_incoming = $this->_systemService->create( $_incoming );
		}

		$this->_processStatistics->created( $_incoming->indexIdentifier() );
		return $_incoming;
	}

	/**
	 * Read the set of system entities to process
	 *    - Override this in extended classes batching, etc
	 *
	 * @return iterable
	 */
	protected function readSystemEntities(): iterable {
		$entities = $this->_externalService->read();
		$this->_processStatistics->incomingTotal( $entities->count() );
		return $entities;
	}

	/**
	 * @param string $_identifier
	 *
	 * @throws SetReaderException
	 * @return IIndexedEntity|null
	 */
	protected function readSystemEntityByIdentifier( string $_identifier ): ?IIndexedEntity {
		return $this->_systemService->readByUniqueIdentifier( $_identifier );
	}

	/**
	 * @inheritDoc
	 */
	public function process( string $_input_stream_uri ): void {
		try {
			$processStartTime = hrtime( true );

			$streamProperties = $this->_externalService->readStream(
				$_input_stream_uri,
				$this->incomingEntityTransformer()
			);

			$this->preProcessValidationEvents( $streamProperties );

			$this->preProcessBanner( $streamProperties );
			if ( false === $this->isStreamProcessValid( $streamProperties ) ) {
				return;
			}

			$this->preProcessEvents();

			/**
			 * @var IIndexedEntity $record
			 */
			foreach ( $this->readSystemEntities() as $record ) {
				$systemEntity    = $this->readSystemEntityByIdentifier( $record->uniqueIdentifier() );
				$hasSystemEntity = !empty( $systemEntity );

				$hasSystemEntity
					? $this->processExistingSystemEntity( $record, $systemEntity )
					: $this->processNewSystemEntity( $record );

				$this->postSystemEntityProcessedEvent( $record );
			}

			$this->postProcessingEvents();
			$this->postProcessingBanner();

			$processEndTime = hrtime( true );
			$elapsedTime    = round( ( $processEndTime - $processStartTime ) / 1e+9, 1 );
			$this->_logger->info( "Process run took $elapsedTime seconds." );
		}
		catch ( Exception $exception ) {
			throw new ImportStreamException(
				"Record set unavailable | {$exception->getMessage()}"
			);
		}
	}
}
