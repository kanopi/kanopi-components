<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\IStreamBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Processor\IBatchProcessor;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\{IIndexedEntityWriter, IStreamBatch, ITrackingIndex};
use Kanopi\Components\Transformers\Arrays;

/**
 * Builds on patterns from Update => Destructive Update processor
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Tracks and removes system entities missing in the incoming stream
 *    - Dynamic batching restarts on changes to batch size or incoming stream properties
 *
 * @package kanopi/components
 */
abstract class BatchedDestructiveUpdate extends DestructiveUpdate implements IBatchProcessor {
	/**
	 * @var IStreamBatch
	 */
	protected IStreamBatch $batchService;
	/**
	 * State of the current process batch configuration
	 *
	 * @var IStreamBatchConfiguration
	 */
	protected IStreamBatchConfiguration $batchConfiguration;
	/**
	 * Whether to force restarting the next batch
	 *
	 * @var bool
	 */
	protected bool $forceBatchRestart = false;
	/**
	 * Requested size for each batch to run
	 *
	 * @var int
	 */
	protected int $requestedBatchSize = 0;

	/**
	 * @param ILogger               $_logger           Logging service
	 * @param IExternalStreamReader $_external_service External source data service
	 * @param IIndexedEntityWriter  $_system_service   Internal target data service
	 * @param ITrackingIndex        $_tracking_service Process status tracking service
	 * @param IStreamBatch          $_batch_service    Process batching service
	 */
	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service,
		ITrackingIndex $_tracking_service,
		IStreamBatch $_batch_service
	) {
		parent::__construct( $_logger, $_external_service, $_system_service, $_tracking_service );
		$this->batchService = $_batch_service;
	}

	/**
	 * {@inheritDoc}
	 */
	public function processBatch( string $_input_stream_uri, int $_batch_size ): void {
		$this->requestedBatchSize = $_batch_size;

		$this->process( $_input_stream_uri );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function deleteAllUnprocessedEntities(): void {
		if ( $this->batchConfiguration->isStreamComplete() ) {
			parent::deleteAllUnprocessedEntities();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool {
		if ( $this->batchConfiguration->isStreamComplete() ) {
			$this->logger()->info( 'No new batches found to process' );
		}

		return false === $this->batchConfiguration->isStreamComplete();
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException
	 */
	protected function postProcessingEvents(): void {
		$this->batchConfiguration->updateBatch( $this->batchConfiguration->currentBatch() );

		$this->logger()->info( 'Updating the stored batch configuration' );
		$this->batchService()->updateByIdentifier( $this->batchStorageUniqueIdentifier(), $this->batchConfiguration );

		parent::postProcessingEvents();

		// Clears the tracking index when the batch is complete
		if ( $this->batchConfiguration->isStreamComplete() ) {
			$this->trackingService()->updateByIdentifier( $this->trackingStorageUniqueIdentifier(), [] );
		}
	}

	/**
	 * Batch configuration service
	 *
	 * @return IStreamBatch
	 */
	protected function batchService(): IStreamBatch {
		return $this->batchService;
	}

	/**
	 * Unique identifier for the persistent storage of the processes current batch
	 *
	 * @return string
	 */
	abstract public function batchStorageUniqueIdentifier(): string;

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessEvents(): void {
		parent::preProcessEvents();

		$loadNewIndex = 2 > $this->batchConfiguration->currentBatch();

		$this->trackingIndex = $this->trackingService()->readTrackingIndexByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			function () {
				return $this->systemService()->read();
			},
			$loadNewIndex
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void {
		// Check for one-shot force batch restart, restart then reset the flag
		if ( $this->forceBatchRestart ) {
			$this->batchService()->forceRestart();
			$this->forceBatchRestart = false;
		}

		$this->batchConfiguration = $this->batchService()->readCurrentByIdentifier(
			$this->batchStorageUniqueIdentifier(),
			$this->requestedBatchSize,
			$_streamProperties
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function forceRestart(): void {
		$this->forceBatchRestart = true;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return $this->batchConfiguration->systemTransform();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessingBannerHeader(): array {
		return Arrays::from(
			[
				'Batch Size',
				'Current Batch',
				'Total Batches',
				'Index Start',
				'Index End',
				'Processed Batches',
			]
		)->append( parent::preProcessingBannerHeader() )->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function readExternalEntities(): iterable {
		$entities = $this->externalService()->read();
		$this->processStatistics->incomingTotal( $entities->count() );
		return $this->batchConfiguration->readCurrentBatch( $entities->getArrayCopy() );
	}
}
