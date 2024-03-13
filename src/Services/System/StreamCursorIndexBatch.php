<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\StreamCursorBatchConfiguration;
use Kanopi\Components\Model\Data\Process\OffsetStreamCursorBatchConfiguration;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\IGroupSetWriter;

/**
 * @package kanopi/components
 */
class StreamCursorIndexBatch implements StreamCursorBatch {
	/**
	 * @var IGroupSetWriter
	 */
	protected IGroupSetWriter $batchStorageRepository;
	/**
	 * When enabled, forces the next requested batch to restart
	 *
	 * @var bool
	 */
	protected bool $restartNextBatch = false;

	/**
	 * @param IGroupSetWriter $_batch_storage_repository Batch information storage
	 */
	public function __construct( IGroupSetWriter $_batch_storage_repository ) {
		$this->batchStorageRepository = $_batch_storage_repository;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetReaderException Unable to read the batch information
	 */
	public function readCurrentByIdentifier(
		string $_unique_identifier,
		int $_batchSize,
		int $_maximumEntities
	): StreamCursorBatchConfiguration {
		try {
			$storedBatch = $this->readStoredBatchConfiguration( $_unique_identifier );
		} catch ( SetReaderException $_exception ) {
			throw new SetReaderException( "Error reading batch storage | {$_exception->getMessage()}" );
		}

		return $storedBatch ?? new OffsetStreamCursorBatchConfiguration( $_batchSize, $_maximumEntities );
	}

	/**
	 * Read any system stored/tracked batch configuration
	 *  - Checks to see if the batch is not started, complete, or restarted, if any is true, returns null
	 *
	 * @param string $_unique_identifier Batch unique identifier
	 *
	 * @return StreamCursorBatchConfiguration|null
	 * @throws SetReaderException Unable to read batch configuration
	 *
	 */
	protected function readStoredBatchConfiguration( string $_unique_identifier ): ?StreamCursorBatchConfiguration {
		// Find the stored batch if a restart is not requested
		$storedBatchSet = $this->batchStorageRepository->read( $_unique_identifier );

		/**
		 * @var StreamCursorBatchConfiguration|null $batch
		 */
		$batch          = $storedBatchSet->valid() ? $storedBatchSet->current() : null;
		$useStoredBatch = ! ( $batch?->isStreamComplete() ?? false ) && ! $this->restartNextBatch;

		// Clear the one-shot restart flag
		$this->restartNextBatch = false;

		return $useStoredBatch ? $batch : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function forceRestart(): void {
		$this->restartNextBatch = true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateByIdentifier(
		string $_unique_identifier,
		StreamCursorBatchConfiguration $_configuration
	): void {
		$this->batchStorageRepository->update( $_unique_identifier, $_configuration );
	}
}
