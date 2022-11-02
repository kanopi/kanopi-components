<?php
/**
 * Data reader interface for streams of data
 */

namespace Kanopi\Components\Repositories;

use InvalidArgumentException;

interface IStreamReader {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param string $_stream_path Path to the input stream
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return string
	 */
	function read( string $_stream_path ): string;
}