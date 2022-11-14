<?php
/**
 * Import data from an input stream
 */

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Exception\ImportStreamException;

interface IImportStream {
	/**
	 * Completes the import process using the supplied data
	 *
	 * @param string $_input_stream_uri
	 *
	 * @throws ImportStreamException
	 *
	 * @return void
	 */
	public function process( string $_input_stream_uri ): void;
}