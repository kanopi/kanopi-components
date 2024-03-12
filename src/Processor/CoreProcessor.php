<?php

namespace Kanopi\Components\Processor;

use Exception;
use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Process\IIndexedProcessStatistics;
use Kanopi\Components\Model\Exception\ImportStreamException;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

trait CoreProcessor {
	/**
	 * External data import source service
	 *
	 * @return IExternalStreamReader
	 */
	abstract protected function externalService(): IExternalStreamReader;

	/**
	 * Logging interface
	 *
	 * @return ILogger
	 */
	abstract protected function logger(): ILogger;

	/**
	 * Statistics for the current process
	 *
	 * @return IIndexedProcessStatistics
	 */
	abstract protected function processStatistics(): IIndexedProcessStatistics;

	/**
	 * Target data store service
	 *
	 * @return IIndexedEntityWriter
	 */
	abstract protected function systemService(): IIndexedEntityWriter;

	/**
	 * Events to execute after all system entities are processed
	 *
	 * @return void
	 */
	abstract protected function postProcessingEvents(): void;

	/**
	 * Events to execute after a system entity is processed
	 *
	 * @param IIndexedEntity $_entity Post-processed system entity
	 *
	 * @return void
	 */
	abstract protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void;

	/**
	 * Read the external data entities
	 *
	 * @param string $_input_stream_uri URI of the input stream
	 *
	 * @return void
	 * @throws ImportStreamException Failure to process import stream
	 */
	abstract protected function processExternalStreamEvents( string $_input_stream_uri ): void;

	/**
	 * Events to execute before processing
	 *
	 * @return void
	 */
	protected function preProcessEvents(): void {
		if ( $this->isDryRunEnabled() ) {
			$this->logger()->info( 'Dry run is enabled for this process' );
		}
	}

	/**
	 * Main event to process the previously read entity set
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
	 *    - Separate to override and process batching, etc
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
		}
		catch ( SetReaderException | SetWriterException $exception ) {
			$message = sprintf(
				'Error processing entity with UID %s: %s',
				$_systemEntity->uniqueIdentifier(),
				$exception->getMessage()
			);

			$this->logger()->error( $message );

			// When stop on error is set, rethrow the error to stop processing
			if ( $this->willStopOnError() ) {
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
		}
		else {
			$this->processStatistics->skipped( $_incoming->indexIdentifier() );
		}

		return $_incoming;
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
		return $this->willAlwaysOverwrite() || $_incoming->version() !== $_existing->version();
	}

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

	/**
	 * Completes the import process using the supplied data
	 *
	 * @param string $_input_stream_uri URI of the input stream
	 *
	 * @return void
	 * @throws ImportStreamException Failure to process import stream
	 */
	public function process( string $_input_stream_uri ): void {
		try {
			$processStartTime = hrtime( true );

			$this->processExternalStreamEvents( $_input_stream_uri );
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
}
