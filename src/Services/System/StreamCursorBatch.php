<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\StreamCursorBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;
use Kanopi\Components\Model\Exception\{SetReaderException, SetWriterException};

/**
 * Batch properties for a stream processor
 *
 * @package kanopi/components
 */
interface StreamCursorBatch {
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
	 * @param string                 $_unique_identifier Batch unique identifier
	 * @param int                    $_batch_size        Size of batches
	 * @param StreamCursorProperties $_properties        External stream properties
	 *
	 * @return StreamCursorBatchConfiguration
	 * @throws SetReaderException Unable to read batch information
	 *
	 */
	public function readCurrentByIdentifier(
		string $_unique_identifier,
		int $_batch_size,
		StreamCursorProperties $_properties
	): StreamCursorBatchConfiguration;

	/**
	 * Update the stored batch configuration for a give identifier
	 *
	 * @param string                         $_unique_identifier New batch unique identifier
	 * @param StreamCursorBatchConfiguration $_configuration     Batch configuration
	 *
	 * @return void
	 * @throws SetWriterException Unable to update the batch configuration
	 *
	 */
	public function updateByIdentifier(
		string $_unique_identifier,
		StreamCursorBatchConfiguration $_configuration
	): void;
}
