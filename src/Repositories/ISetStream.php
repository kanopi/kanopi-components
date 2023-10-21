<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Model\Data\Stream\IStreamCollection;
use Kanopi\Components\Model\Exception\SetStreamException;

/**
 * Reads an input stream into an iterable collection
 *
 * @package kanopi/components
 */
interface ISetStream {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param IStream $_input_stream Incoming data stream
	 *
	 * @return IStreamCollection
	 * @throws SetStreamException Unable to read incoming data stream
	 *
	 */
	public function read( IStream $_input_stream ): IStreamCollection;
}
