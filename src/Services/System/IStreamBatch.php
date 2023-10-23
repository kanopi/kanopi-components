<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\IStreamBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\{SetReaderException, SetWriterException};

/**
 * Batch properties for a stream processor
 *
 * @package kanopi/components
 */
interface IStreamBatch {
	/**
	 * Force restart the next requested batch
	 *
	 * @return void
	 */
	public function forceRestart(): void;

	/**
	 * Read the current batch configuration by identifier
	 *    - Compares against stored batch configurations
	 *    - Continues matching batch or starts a new batch
	 *
	 * @param string            $_unique_identifier Batch unique identifier
	 * @param int               $_batch_size        Size of batches
	 * @param IStreamProperties $_properties        External stream properties
	 *
	 * @return IStreamBatchConfiguration
	 * @throws SetReaderException Unable to read batch information
	 *
	 */
	public function readCurrentByIdentifier(
		string $_unique_identifier,
		int $_batch_size,
		IStreamProperties $_properties
	): IStreamBatchConfiguration;

	/**
	 * Update the stored batch configuration for a give identifier
	 *
	 * @param string                    $_unique_identifier New batch unique identifier
	 * @param IStreamBatchConfiguration $_configuration     Batch configuration
	 *
	 * @return void
	 * @throws SetWriterException Unable to update the batch configuration
	 *
	 */
	public function updateByIdentifier( string $_unique_identifier, IStreamBatchConfiguration $_configuration ): void;
}
