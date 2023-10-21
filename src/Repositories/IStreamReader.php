<?php

namespace Kanopi\Components\Repositories;

use InvalidArgumentException;
use Kanopi\Components\Model\Data\Stream\IStream;

/**
 * Data reader interface for streams of data
 *
 * @package kanopi/components
 */
interface IStreamReader {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param string $_stream_path Path to the input stream
	 *
	 * @return IStream
	 * @throws InvalidArgumentException Invalid stream URI
	 *
	 */
	public function read( string $_stream_path ): IStream;
}
