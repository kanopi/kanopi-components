<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\IIndexedProcessStatistics;
use Kanopi\Components\Model\Data\Process\IndexedProcessStatistics;
use Kanopi\Components\Processor\CursorBatchProcessor;
use Kanopi\Components\Processor\CursorBatchStream;
use Kanopi\Components\Services\External\ExternalCursorStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\ITrackingIndex;
use Kanopi\Components\Services\System\StreamCursorBatch;

/**
 * Base implementation to Update entities for an external source
 *
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Maintains process state statistics for all processed entities
 *    - Requires implementation of 'batchStorageUniqueIdentifier', 'createSystemEntity',
 *      'incomingEntityTransformer', and 'trackingStorageUniqueIdentifier'
 *
 * @package kanopi/components
 */
abstract class BatchedCursorUpdate implements CursorBatchProcessor {
	use CursorBatchStream;

	/**
	 * Batch tracking service
	 *
	 * @var StreamCursorBatch
	 */
	protected StreamCursorBatch $batchService;
	/**
	 * External data import source service
	 *
	 * @var ExternalCursorStreamReader
	 */
	protected ExternalCursorStreamReader $externalService;
	/**
	 * @var ILogger
	 */
	protected ILogger $logger;
	/**
	 * Statistics for the current process
	 *
	 * @var IIndexedProcessStatistics
	 */
	protected IIndexedProcessStatistics $processStatistics;
	/**
	 * Target data store service
	 *
	 * @var IIndexedEntityWriter
	 */
	protected IIndexedEntityWriter $systemService;
	/**
	 * Process action tracking service
	 *
	 * @var ITrackingIndex
	 */
	protected ITrackingIndex $trackingService;

	/**
	 * @param ILogger                    $_logger          Logging service
	 * @param ExternalCursorStreamReader $_externalService External source data service
	 * @param IIndexedEntityWriter       $_systemService   Internal target data service
	 * @param ITrackingIndex             $_trackingService Process action tracking service
	 * @param StreamCursorBatch          $_batchService    Batch tracking service
	 */
	public function __construct(
		ILogger $_logger,
		ExternalCursorStreamReader $_externalService,
		IIndexedEntityWriter $_systemService,
		ITrackingIndex $_trackingService,
		StreamCursorBatch $_batchService
	) {
		$this->processStatistics = new IndexedProcessStatistics();

		$this->batchService    = $_batchService;
		$this->logger          = $_logger;
		$this->externalService = $_externalService;
		$this->systemService   = $_systemService;
		$this->trackingService = $_trackingService;
	}

	/**
	 * Batch tracking service
	 *
	 * @return StreamCursorBatch
	 */
	protected function batchService(): StreamCursorBatch {
		return $this->batchService;
	}

	/**
	 * External data import source service
	 *
	 * @return ExternalCursorStreamReader
	 */
	protected function externalService(): ExternalCursorStreamReader {
		return $this->externalService;
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
	 * Process statistics
	 *
	 * @return IndexedProcessStatistics
	 */
	protected function processStatistics(): IndexedProcessStatistics {
		return $this->processStatistics;
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
	 * Process action tracking service
	 *
	 * @return ITrackingIndex
	 */
	protected function trackingService(): ITrackingIndex {
		return $this->trackingService;
	}
}
