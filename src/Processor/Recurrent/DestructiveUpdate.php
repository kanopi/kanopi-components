<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\ITrackingIndex;
use Kanopi\Components\Transformers\Arrays;

/**
 * Builds on patterns from Update processor
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Tracks and removes system entities missing in the incoming stream
 */
abstract class DestructiveUpdate extends Update {
	/**
	 * Options interface to track batch progress
	 *
	 * @var ITrackingIndex
	 */
	protected ITrackingIndex $_trackingService;

	/**
	 * Tracking index of (ID => Processed Boolean Flag)
	 *
	 * @var array
	 */
	protected array $_trackingIndex = [];

	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service,
		ITrackingIndex $_tracking_service
	) {
		parent::__construct( $_logger, $_external_service, $_system_service );
		$this->_trackingService = $_tracking_service;
	}

	/**
	 * Options interface to track progress
	 *
	 * @return ITrackingIndex
	 */
	protected function trackingService(): ITrackingIndex {
		return $this->_trackingService;
	}

	/**
	 * Storage key for the tracking index of each given process
	 *
	 * @return string
	 */
	abstract protected function trackingStorageUniqueIdentifier(): string;

	/**
	 * Create a new system entity, used as a proxy for the delete action
	 *
	 * @return IIndexedEntity
	 */
	abstract protected function createSystemEntity(): IIndexedEntity;

	/**
	 * Delete all unprocessed entities
	 *
	 * @return void
	 */
	protected function deleteAllUnprocessedEntities(): void {
		foreach ( $this->_trackingIndex as $_id => $_was_processed ) {
			if ( $_was_processed || 0 === $_id ) {
				continue;
			}

			$this->logger()->verbose( "Removing system entity with ID $_id" );
			$proxyEntity = $this->createSystemEntity();
			$proxyEntity->updateIndexIdentifier( $_id );

			if ( false === $this->isDryRunEnabled() ) {
				$this->systemService()->delete( $proxyEntity );
			}

			$this->_processStatistics->deleted( $proxyEntity->indexIdentifier() );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function postProcessingBannerDataRow(): array {
		return Arrays::from( parent::postProcessingBannerDataRow() )
			->append( [
				'Deleted' => $this->_processStatistics->deletedAmount()
			] )->toArray();
	}

	/**
	 * @inheritDoc
	 */
	protected function postProcessingBannerHeader(): array {
		return Arrays::from( parent::postProcessingBannerHeader() )
			->append( [
				'Deleted'
			] )->toArray();
	}

	/**
	 * @inheritDoc
	 * @throws SetWriterException
	 */
	protected function postProcessingEvents(): void {
		$this->logger()->info( "Updating the stored tracking index" );
		$this->trackingService()->updateByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			$this->_trackingIndex
		);

		$this->deleteAllUnprocessedEntities();
	}

	/**
	 * @inheritDoc
	 */
	protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void {
		if ( 0 < $_entity->indexIdentifier() ) {
			$this->_trackingIndex[ $_entity->indexIdentifier() ] = true;
		}
	}

	/**
	 * @inheritDoc
	 * @throws SetReaderException
	 */
	protected function preProcessEvents(): void {
		$this->_trackingIndex = $this->trackingService()->readTrackingIndexByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			function () {
				return $this->systemService()->read();
			},
			true
		);
	}
}
