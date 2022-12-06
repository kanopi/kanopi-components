<?php
/**
 * Data reader interface for streams of data
 */

namespace Kanopi\Components\Repositories;

use InvalidArgumentException;
use Kanopi\Components\Model\Data\IStream;

interface IStreamReader {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param string $_stream_path Path to the input stream
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return IStream
	 */
	function read( string $_stream_path ): IStream;
}