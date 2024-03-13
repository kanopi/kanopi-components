<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Process\StreamCursorBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;
use Kanopi\Components\Model\Exception\ImportStreamException;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\External\ExternalCursorStreamReader;
use Kanopi\Components\Services\System\StreamCursorBatch;

/**
 * Reads and validates an external cursor stream
 */
trait CursorBatchStream {
	use DestructiveProcessor;

	/**
	 * State of the current process batch configuration
	 *
	 * @var StreamCursorBatchConfiguration|null
	 */
	protected ?StreamCursorBatchConfiguration $batchConfiguration;
	/**
	 * Whether to force restarting the next batch
	 *
	 * @var bool
	 */
	protected bool $forceBatchRestart = false;
	/**
	 * State of the current process batch configuration
	 *
	 * @var StreamCursorProperties|null
	 */
	protected ?StreamCursorProperties $currentBatchProperties;

	/**
	 * Batch status tracking service
	 *
	 * @return StreamCursorBatch
	 */
	abstract protected function batchService(): StreamCursorBatch;

	/**
	 * Unique identifier for the persistent storage of the processes current batch
	 *
	 * @return string
	 */
	abstract public function batchStorageUniqueIdentifier(): string;

	/**
	 * External data import source service
	 *
	 * @return ExternalCursorStreamReader
	 */
	abstract protected function externalService(): ExternalCursorStreamReader;

	/**
	 * Entity transformer for all incoming data
	 *
	 * @return IEntitySet
	 */
	abstract protected function incomingEntityTransformer(): IEntitySet;

	/**
	 * Logging interface
	 *
	 * @return ILogger
	 */
	abstract protected function logger(): ILogger;

	/**
	 * Stream validity check
	 *    - Return false to stop processing
	 *
	 * @return bool
	 */
	abstract protected function isStreamProcessValid(): bool;

	/**
	 * Events before process validation
	 *
	 * @return void
	 * @throws SetReaderException Could not validate input set
	 */
	abstract protected function preProcessValidationEvents(): void;

	/**
	 * Process a cursor in batches of a given size
	 *  - Assumes the processor uses a cursor offset/start index internally to track progress
	 *
	 * @param string $_inputStreamUri  URI path of input stream
	 * @param int    $_batchSize       Size of each batch
	 * @param int    $_maximumEntities Maximum total entities to return
	 *
	 * @return void
	 * @throws SetReaderException | ImportStreamException Failure to process import stream
	 */
	public function processBatch(
		string $_inputStreamUri,
		int $_batchSize,
		int $_maximumEntities
	): void {
		if ( $this->forceBatchRestart ) {
			$this->batchService()->forceRestart();
		}

		$this->batchConfiguration = $this->batchService()->readCurrentByIdentifier(
			$this->batchStorageUniqueIdentifier(),
			$_batchSize,
			$_maximumEntities
		);

		$this->process( $_inputStreamUri );
	}

	/**
	 * Read the next batch of external data entities based on the last saved cursor offset
	 *
	 * @param string $_inputStreamUri URI of the input stream
	 *
	 * @return void
	 *
	 * @throws SetStreamException Failure to process the import stream
	 */
	protected function processExternalStreamEvents( string $_inputStreamUri ): void {
		try {
			$this->currentBatchProperties = $this->readExternalStream( $_inputStreamUri );

			$this->preProcessValidationEvents();
			$this->preProcessBanner();

			if ( false === $this->isStreamProcessValid() ) {
				throw new SetStreamException( 'External stream is invalid' );
			}
		} catch ( SetReaderException | SetStreamException $exception ) {
			throw new SetStreamException( $exception->getMessage() );
		}
	}

	/**
	 * Read the external data stream
	 *  - Separate to override as needed for alternate external data sources
	 *
	 * @param string $_inputStreamUri External string URI
	 *
	 * @return StreamCursorProperties Processed stream with properties for next batch
	 * @throws SetStreamException Failure to read external import stream
	 *
	 */
	protected function readExternalStream(
		string $_inputStreamUri
	): StreamCursorProperties {
		return $this->externalService()->readStream(
			$_inputStreamUri,
			$this->batchConfiguration->currentPage(),
			$this->incomingEntityTransformer()
		);
	}

	/**
	 * Logging banner to show before processing begins
	 *
	 * @return void
	 */
	protected function preProcessBanner(): void {
		$this->logger()->table(
			[
				'Stream Uri',
				'Complete?',
				'Start Offset',
				'Next Offset',
			],
			[
				[
					'Stream Uri'   => $this->currentBatchProperties->uri(),
					'Start Offset' => $this->currentBatchProperties->offsetStart() ?? '-',
					'Next Offset'  => $this->currentBatchProperties->offsetNext() ?? '-',
					'Last Batch'   => $this->currentBatchProperties->isStreamCursorComplete() ? 'Yes' : 'No',
				],
			]
		);
	}
}
