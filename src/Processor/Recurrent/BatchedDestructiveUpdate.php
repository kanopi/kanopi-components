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
 */
abstract class BatchedDestructiveUpdate extends DestructiveUpdate implements IBatchProcessor {
	/**
	 * @var IStreamBatch
	 */
	protected IStreamBatch $_batchService;

	/**
	 * State of the current process batch configuration
	 *
	 * @var IStreamBatchConfiguration
	 */
	protected IStreamBatchConfiguration $_batchConfiguration;

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
	 * @param ILogger               $_logger
	 * @param IExternalStreamReader $_external_service
	 * @param IIndexedEntityWriter  $_system_service
	 * @param ITrackingIndex        $_tracking_service
	 * @param IStreamBatch          $_batch_service
	 */
	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service,
		ITrackingIndex $_tracking_service,
		IStreamBatch $_batch_service
	) {
		parent::__construct( $_logger, $_external_service, $_system_service, $_tracking_service );
		$this->_batchService = $_batch_service;
	}
	
	/**
	 * Batch configuration service
	 *
	 * @return IStreamBatch
	 */
	protected function batchService(): IStreamBatch {
		return $this->_batchService;
	}

	/**
	 * Unique identifier for the persistent storage of the processes current batch
	 *
	 * @return string
	 */
	abstract function batchStorageUniqueIdentifier(): string;

	/**
	 * {@inheritDoc}
	 */
	protected function deleteAllUnprocessedEntities(): void {
		if ( $this->_batchConfiguration->isStreamComplete() ) {
			parent::deleteAllUnprocessedEntities();
		}
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
	protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool {
		if ( $this->_batchConfiguration->isStreamComplete() ) {
			$this->logger()->info( "No new batches found to process" );
		}

		return false === $this->_batchConfiguration->isStreamComplete();
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException
	 */
	protected function postProcessingEvents(): void {
		$this->_batchConfiguration->updateBatch( $this->_batchConfiguration->currentBatch() );

		$this->logger()->info( "Updating the stored batch configuration" );
		$this->batchService()->updateByIdentifier( $this->batchStorageUniqueIdentifier(), $this->_batchConfiguration );

		parent::postProcessingEvents();

		// Clears the tracking index when the batch is complete
		if ( $this->_batchConfiguration->isStreamComplete() ) {
			$this->trackingService()->updateByIdentifier( $this->trackingStorageUniqueIdentifier(), [] );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessEvents(): void {
		parent::preProcessEvents();

		$loadNewIndex = 2 > $this->_batchConfiguration->currentBatch();

		$this->_trackingIndex = $this->trackingService()->readTrackingIndexByIdentifier(
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

		$this->_batchConfiguration = $this->batchService()->readCurrentByIdentifier(
			$this->batchStorageUniqueIdentifier(),
			$this->requestedBatchSize,
			$_streamProperties
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return $this->_batchConfiguration->systemTransform();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function preProcessingBannerHeader(): array {
		return Arrays::from( [
			'Batch Size',
			'Current Batch',
			'Total Batches',
			'Index Start',
			'Index End',
			'Processed Batches',
		] )->append( parent::preProcessingBannerHeader() )->toArray();
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
	protected function readSystemEntities(): iterable {
		$entities = $this->externalService()->read();
		$this->_processStatistics->incomingTotal( $entities->count() );
		return $this->_batchConfiguration->readCurrentBatch( $entities->getArrayCopy() );
	}
}
