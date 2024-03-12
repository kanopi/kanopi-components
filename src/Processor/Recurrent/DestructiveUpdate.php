<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\IIndexedProcessStatistics;
use Kanopi\Components\Model\Data\Process\IndexedProcessStatistics;
use Kanopi\Components\Processor\DestructiveProcessor;
use Kanopi\Components\Processor\DryRunProcessor;
use Kanopi\Components\Processor\IDryRunProcessor;
use Kanopi\Components\Processor\ProcessorStates;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\ITrackingIndex;

/**
 * Base implementation to Update entities for an external source, and remote any system entries not on the external
 *
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Tracks and removes system entities missing in the incoming stream
 *    - Requires implementation of 'createSystemEntity', 'incomingEntityTransformer', 'isStreamProcessValid',
 *        'preProcessValidationEvents', 'processExternalStreamEvents', and 'trackingStorageUniqueIdentifier'
 *
 * @package kanopi/components
 */
abstract class DestructiveUpdate implements IDryRunProcessor {
	use DestructiveProcessor;
	use DryRunProcessor;
	use ProcessorStates;

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
	 * Options interface to track batch progress
	 *
	 * @var ITrackingIndex
	 */
	protected ITrackingIndex $trackingService;

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
		$this->processStatistics = new IndexedProcessStatistics();

		$this->logger          = $_logger;
		$this->externalService = $_external_service;
		$this->systemService   = $_system_service;
		$this->trackingService = $_tracking_service;
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
		return $this->processStatistics();
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
	 * Options interface to track progress
	 *
	 * @return ITrackingIndex
	 */
	protected function trackingService(): ITrackingIndex {
		return $this->trackingService;
	}
}
