<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\System\ITrackingIndex;

/**
 * Processor to remove any entities in the system which are not found in the external stream.
 * Useful when the external stream is the source of truth for the data set.
 *
 * @package kanopi/components
 */
trait DestructiveProcessor {
	use CoreProcessor {
		preProcessEvents as corePreProcessEvents;
	}

	/**
	 * Create a new system entity, used as a proxy for the delete action
	 *
	 * @return IIndexedEntity
	 */
	abstract protected function createSystemEntity(): IIndexedEntity;

	/**
	 * Options interface to track progress
	 *
	 * @return ITrackingIndex
	 */
	abstract protected function trackingService(): ITrackingIndex;

	/**
	 * Storage key for the tracking index of each given process
	 *
	 * @return string
	 */
	abstract protected function trackingStorageUniqueIdentifier(): string;

	/**
	 * Tracking index of (ID => Processed Boolean Flag)
	 *
	 * @var array
	 */
	protected array $trackingIndex = [];

	/**
	 * Events to execute before processing
	 *
	 * @throws SetReaderException Unable to read the tracking index
	 */
	protected function preProcessEvents(): void {
		$this->corePreProcessEvents();

		$this->trackingIndex = $this->trackingService()->readTrackingIndexByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			function () {
				return $this->systemService()->read();
			},
			true
		);
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException
	 */
	protected function postProcessingEvents(): void {
		$this->logger()->info( 'Updating the stored tracking index' );
		$this->trackingService()->updateByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			$this->trackingIndex
		);

		$this->deleteAllUnprocessedEntities();
	}

	/**
	 * Delete all unprocessed entities
	 *
	 * @throws SetWriterException Cannot delete the entity
	 * @return void
	 */
	protected function deleteAllUnprocessedEntities(): void {
		foreach ( $this->trackingIndex as $_id => $_was_processed ) {
			if ( $_was_processed || 0 === $_id ) {
				continue;
			}

			$this->logger()->verbose( "Removing system entity with ID $_id" );
			$proxyEntity = $this->createSystemEntity();
			$proxyEntity->updateIndexIdentifier( $_id );

			if ( false === $this->isDryRunEnabled() ) {
				$this->systemService()->delete( $proxyEntity );
			}

			$this->processStatistics->deleted( $proxyEntity->indexIdentifier() );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void {
		if ( 0 < $_entity->indexIdentifier() ) {
			$this->trackingIndex[ $_entity->indexIdentifier() ] = true;
		}
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
			'Deleted',
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
			'Deleted'   => $this->processStatistics->deletedAmount(),
			'Skipped'   => $this->processStatistics->skippedAmount(),
			'Processed' => $this->processStatistics->processedTotalAmount(),
		];
	}
}
