<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Exception\ImportStreamException;

/**
 * Import stream processor batching controls
 *
 * @package kanopi/components
 */
interface CursorBatchProcessor extends IImportStream, DeleteProcessor {
	/**
	 * Restarts the next processed batch
	 *
	 * @return void
	 */
	public function forceRestart(): void;

	/**
	 * Process a cursor in batches of a given size
	 *  - Assumes the processor uses a cursor offset/start index internally to track progress
	 *
	 * @param string $_inputStreamUri  URI path of input stream
	 * @param int    $_batchSize       Size of each batch
	 * @param int    $_maximumEntities Maximum total entities to return
	 *
	 * @return void
	 * @throws ImportStreamException Failure to process import stream
	 */
	public function processBatch(
		string $_inputStreamUri,
		int $_batchSize,
		int $_maximumEntities
	): void;
}
