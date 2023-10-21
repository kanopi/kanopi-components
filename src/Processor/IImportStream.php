<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Model\Exception\ImportStreamException;

/**
 * Import data from an input stream
 *
 * @package kanopi/components
 */
interface IImportStream {
	/**
	 * Change whether to force overwriting any existing content
	 *  - Defaults to leave existing content (Off)
	 *
	 * @param bool $_enableState Whether to overwrite or not
	 *
	 * @return void
	 */
	public function changeOverwriteStatus( bool $_enableState ): void;

	/**
	 * Change whether to stop processing when there's an error processing any entity
	 *  - Defaults to log the error and continue processing other entities (Off)
	 *
	 * @param bool $_enableState Whether to stop on an error
	 *
	 * @return void
	 */
	public function changeStopOnError( bool $_enableState ): void;

	/**
	 * Completes the import process using the supplied data
	 *
	 * @param string $_input_stream_uri URI of the input strea
	 *
	 * @return void
	 * @throws ImportStreamException Failure to process import stream
	 */
	public function process( string $_input_stream_uri ): void;
}
