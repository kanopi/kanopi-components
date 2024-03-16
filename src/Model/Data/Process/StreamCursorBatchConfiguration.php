<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\StreamCursorPagination;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;

/**
 * Batch configuration for import/update processes using a cursor to track the next starting offset/index
 *
 * @package kanopi/components
 */
interface StreamCursorBatchConfiguration extends IIndexedEntity {
	/**
	 * @return StreamCursorPagination
	 */
	public function currentPage(): StreamCursorPagination;

	/**
	 * Check to see if there are any more stream batches to read
	 *
	 * @return bool
	 */
	public function isStreamComplete(): bool;

	/**
	 * Set of processed batch indicators
	 *
	 * @return array
	 */
	public function processedBatches(): array;

	/**
	 * Process updates to the cursor stream with the last run/current batches properties
	 *
	 * @param StreamCursorProperties $_properties Current stream batch properties
	 *
	 * @return void
	 */
	public function processCurrentBatch( StreamCursorProperties $_properties ): void;
}
