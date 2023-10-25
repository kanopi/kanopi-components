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
 *
 * @package kanopi/components
 */
abstract class DestructiveUpdate extends Update {
	/**
	 * Options interface to track batch progress
	 *
	 * @var ITrackingIndex
	 */
	protected ITrackingIndex $trackingService;
	/**
	 * Tracking index of (ID => Processed Boolean Flag)
	 *
	 * @var array
	 */
	protected array $trackingIndex = [];

	/**
	 * @param ILogger               $_logger           Logging service
	 * @param IExternalStreamReader $_external_service External source data service
	 * @param IIndexedEntityWriter  $_system_service   Internal target data service
	 * @param ITrackingIndex        $_tracking_service Process status tracking service
	 */
	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service,
		ITrackingIndex $_tracking_service
	) {
		parent::__construct( $_logger, $_external_service, $_system_service );
		$this->trackingService = $_tracking_service;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function postProcessingBannerDataRow(): array {
		return Arrays::from( parent::postProcessingBannerDataRow() )
			->append(
				[
					'Deleted' => $this->processStatistics->deletedAmount(),
				]
			)->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function postProcessingBannerHeader(): array {
		return Arrays::from( parent::postProcessingBannerHeader() )
			->append(
				[
					'Deleted',
				]
			)->toArray();
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
	 * Options interface to track progress
	 *
	 * @return ITrackingIndex
	 */
	protected function trackingService(): ITrackingIndex {
		return $this->trackingService;
	}

	/**
	 * Storage key for the tracking index of each given process
	 *
	 * @return string
	 */
	abstract protected function trackingStorageUniqueIdentifier(): string;

	/**
	 * Delete all unprocessed entities
	 *
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
	 * Create a new system entity, used as a proxy for the delete action
	 *
	 * @return IIndexedEntity
	 */
	abstract protected function createSystemEntity(): IIndexedEntity;

	/**
	 * {@inheritDoc}
	 */
	protected function postSystemEntityProcessedEvent( IIndexedEntity $_entity ): void {
		if ( 0 < $_entity->indexIdentifier() ) {
			$this->trackingIndex[ $_entity->indexIdentifier() ] = true;
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws SetReaderException
	 */
	protected function preProcessEvents(): void {
		$this->trackingIndex = $this->trackingService()->readTrackingIndexByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			function () {
				return $this->systemService()->read();
			},
			true
		);
	}
}
