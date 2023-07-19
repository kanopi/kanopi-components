<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\IStreamBatchConfiguration;
use Kanopi\Components\Model\Data\Process\StreamBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\IGroupSetWriter;

class StreamBatch implements IStreamBatch {
	/**
	 * @var IGroupSetWriter
	 */
	protected IGroupSetWriter $_batchStorageRepository;

	/**
	 * When enabled, forces the next requested batch to restart
	 *
	 * @var bool
	 */
	protected bool $restartNextBatch = false;

	/**
	 * @param IGroupSetWriter $_batch_storage_repository
	 */
	public function __construct( IGroupSetWriter $_batch_storage_repository ) {
		$this->_batchStorageRepository = $_batch_storage_repository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readCurrentByIdentifier(
		string $_unique_identifier,
		int $_batch_size,
		IStreamProperties $_properties
	): IStreamBatchConfiguration {
		$batch = new StreamBatchConfiguration( $_batch_size );

		try {
			$storedBatch = $this->readStoredBatchConfiguration( $_unique_identifier );
		}
		catch ( SetReaderException $_exception ) {
			throw new SetReaderException( "Error reading batch storage | {$_exception->getMessage()}" );
		}

		if (
			null !== $storedBatch
			&& $batch->batchSize() === $storedBatch->batchSize()
			&& $_properties->isSameStream( $storedBatch->streamProperties() )
		) {
			$batch = $storedBatch;
		}

		$batch->updateStreamProperties( $_properties );

		return $batch;
	}

	/**
	 * Read any system stored/tracked batch configuration
	 *
	 * @param string $_unique_identifier
	 *
	 * @throws SetReaderException
	 *
	 * @return IStreamBatchConfiguration|null
	 */
	protected function readStoredBatchConfiguration( string $_unique_identifier ): ?IStreamBatchConfiguration {
		// Find the stored batch if a restart is not requested
		$resumeBatch = $this->restartNextBatch ? null : $this->_batchStorageRepository->read( $_unique_identifier );

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
		IStreamBatchConfiguration $_configuration
	): void {
		$this->_batchStorageRepository->update( $_unique_identifier, $_configuration );
	}
}
