<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\StreamCursorBatchConfiguration;
use Kanopi\Components\Model\Data\Process\StreamCursorIndexBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;
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
		int $_batch_size,
		StreamCursorProperties $_properties
	): StreamCursorBatchConfiguration {
		$batch = new StreamCursorIndexBatchConfiguration( $_batch_size );

		try {
			$storedBatch = $this->readStoredBatchConfiguration( $_unique_identifier );
		} catch ( SetReaderException $_exception ) {
			throw new SetReaderException( "Error reading batch storage | {$_exception->getMessage()}" );
		}

		if ( null !== $storedBatch && ! $batch->isStreamComplete() ) {
			$batch = $storedBatch;
		}

		$batch->updateStreamProperties( $_properties );

		return $batch;
	}

	/**
	 * Read any system stored/tracked batch configuration
	 *
	 * @param string $_unique_identifier Batch unique identifier
	 *
	 * @return StreamCursorBatchConfiguration|null
	 * @throws SetReaderException Unable to read batch configuration
	 *
	 */
	protected function readStoredBatchConfiguration( string $_unique_identifier ): ?StreamCursorBatchConfiguration {
		// Find the stored batch if a restart is not requested
		$resumeBatch = $this->restartNextBatch ? null : $this->batchStorageRepository->read( $_unique_identifier );

		// Clear the one-shot restart flag
		$this->restartNextBatch = false;

		return ! empty( $resumeBatch ) && $resumeBatch->valid() ? $resumeBatch->current() : null;
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
