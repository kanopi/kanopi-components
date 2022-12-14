<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Exception\ImportStreamException;

interface IBatchProcessor extends IImportStream {
	/**
	 * Process batches of a given size
	 *  - Assumes the processor tracks the next batch sequentially, from beginning to end
	 *
	 * @param string   $_input_stream_uri
	 * @param int      $_batch_size
	 *
	 * @throws ImportStreamException
	 *
	 * @return void
	 */
	function processBatch( string $_input_stream_uri, int $_batch_size ): void;
}
