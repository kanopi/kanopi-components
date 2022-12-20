<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\IStreamBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Processor\IBatchProcessor;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\IStreamBatch;
use Kanopi\Components\Services\System\ITrackingIndex;
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
	 * @var IStreamBatchConfiguration
	 */
	protected IStreamBatchConfiguration $_batchConfiguration;

	/**
	 * @var int
	 */
	protected int $_requestedBatchSize = 0;

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
	 * Unique identifier for the persistent storage of the processes current batch
	 *
	 * @return string
	 */
	abstract function batchStorageUniqueIdentifier(): string;

	/**
	 * @inheritDoc
	 */
	protected function deleteAllUnprocessedEntities(): void {
		if ( $this->_batchConfiguration->isStreamComplete() ) {
			parent::deleteAllUnprocessedEntities();
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool {
		if ( $this->_batchConfiguration->isStreamComplete() ) {
			$this->_logger->info( "No new batches found to process" );
		}

		return false === $this->_batchConfiguration->isStreamComplete();
	}

	/**
	 * @inheritDoc
	 * @throws SetWriterException
	 */
	protected function postProcessingEvents(): void {
		$this->_batchConfiguration->updateBatch( $this->_batchConfiguration->currentBatch() );

		$this->_logger->info( "Updating the stored batch configuration" );
		$this->_batchService->updateByIdentifier( $this->batchStorageUniqueIdentifier(), $this->_batchConfiguration );

		parent::postProcessingEvents();
	}

	/**
	 * @inheritDoc
	 */
	protected function preProcessEvents(): void {
		parent::preProcessEvents();
		$this->_trackingIndex = $this->_trackingService->readTrackingIndexByIdentifier(
			$this->trackingStorageUniqueIdentifier(),
			function () {
				return $this->_systemService->read();
			},
			$this->_batchConfiguration->currentBatch()
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void {
		$this->_batchConfiguration = $this->_batchService->readCurrentByIdentifier(
			$this->batchStorageUniqueIdentifier(),
			$this->_requestedBatchSize,
			$_streamProperties
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return $this->_batchConfiguration->systemTransform();
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 */
	public function processBatch( string $_input_stream_uri, int $_batch_size ): void {
		$this->_requestedBatchSize = $_batch_size;

		$this->process( $_input_stream_uri );
	}

	/**
	 * @inheritDoc
	 */
	protected function readSystemEntities(): iterable {
		$entities = $this->_externalService->read();
		$this->_processStatistics->incomingTotal( $entities->count() );
		return $this->_batchConfiguration->readCurrentBatch( $entities->getArrayCopy() );
	}
}
