<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Exception\ImportStreamException;

/**
 * Import stream processor batching controls
 *
 * @package kanopi/components
 */
interface IBatchProcessor extends IImportStream {
	/**
	 * Restarts the next processed batch
	 *
	 * @return void
	 */
	public function forceRestart(): void;

	/**
	 * Process batches of a given size
	 *  - Assumes the processor tracks the next batch sequentially, from beginning to end
	 *
	 * @param string $_input_stream_uri URI path of input stream
	 * @param int    $_batch_size       Size of each batch
	 *
	 * @return void
	 * @throws ImportStreamException Failure to process import stream
	 */
	public function processBatch( string $_input_stream_uri, int $_batch_size ): void;
}
