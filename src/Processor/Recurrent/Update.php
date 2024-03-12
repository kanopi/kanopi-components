<?php

namespace Kanopi\Components\Processor\Recurrent;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\IIndexedProcessStatistics;
use Kanopi\Components\Model\Data\Process\IndexedProcessStatistics;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Processor\CoreProcessor;
use Kanopi\Components\Processor\DryRunProcessor;
use Kanopi\Components\Processor\IDryRunProcessor;
use Kanopi\Components\Processor\ProcessorStates;
use Kanopi\Components\Services\External\IExternalStreamReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * Base pattern for Updatable processing
 *    - Creates/Updates entities in the incoming, external stream against the target system
 *    - Maintains process state statistics for all processed entities
 *
 * @package kanopi/components
 */
abstract class Update implements IDryRunProcessor {
	use CoreProcessor;
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
	 * @param ILogger               $_logger           Logging service
	 * @param IExternalStreamReader $_external_service External source data service
	 * @param IIndexedEntityWriter  $_system_service   Internal target data service
	 */
	public function __construct(
		ILogger $_logger,
		IExternalStreamReader $_external_service,
		IIndexedEntityWriter $_system_service
	) {
		$this->processStatistics = new IndexedProcessStatistics();

		$this->logger          = $_logger;
		$this->externalService = $_external_service;
		$this->systemService   = $_system_service;
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
	 * Entity transformer for all incoming data
	 *
	 * @return IEntitySet
	 */
	abstract protected function incomingEntityTransformer(): IEntitySet;

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
	 * Read the external data entities
	 *
	 * @param string $_input_stream_uri URI of the input stream
	 *
	 * @return void
	 *
	 * @throws SetStreamException Failure to process the import stream
	 */
	protected function processExternalStreamEvents( string $_input_stream_uri ): void {
		try {
			$streamProperties = $this->readExternalStream( $_input_stream_uri );

			$this->preProcessValidationEvents( $streamProperties );
			$this->preProcessBanner( $streamProperties );
			if ( false === $this->isStreamProcessValid( $streamProperties ) ) {
				throw new SetStreamException( 'External stream is invalid' );
			}
		}
		catch ( SetReaderException | SetStreamException $exception ) {
			throw new SetStreamException( $exception->getMessage() );
		}
	}

	/**
	 * Read the external data stream
	 *  - Separate to override as needed for alternate external data sources
	 *
	 * @param string $_input_stream_uri External string URI
	 *
	 * @return IStreamProperties Processed stream with properties
	 * @throws SetStreamException Failure to read external import stream
	 *
	 */
	protected function readExternalStream( string $_input_stream_uri ): IStreamProperties {
		return $this->externalService()->readStream( $_input_stream_uri, $this->incomingEntityTransformer() );
	}

	/**
	 * Events before process validation
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 * @throws SetReaderException Could not validate input set
	 *
	 */
	abstract protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void;

	/**
	 * Logging banner to show before processing begins
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 */
	protected function preProcessBanner( IStreamProperties $_streamProperties ): void {
		$this->logger()->table(
			$this->preProcessingBannerHeader(),
			[
				$this->preProcessingBannerDataRow( $_streamProperties ),
			]
		);
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
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return string[]
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return [
			'Stream Last Modified Date' => gmdate( 'm-d-Y H:i:s', $_streamProperties->lastModifiedTimestamp() ),
			'Stream Length'             => $_streamProperties->length(),
			'Stream Uri'                => $_streamProperties->uri(),
		];
	}

	/**
	 * Stream validity check
	 *    - Return false to stop processing
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return bool
	 */
	abstract protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool;
}
