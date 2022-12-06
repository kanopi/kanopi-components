<?php
/**
 * Reads an input stream into an iterable collection
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Data\IStream;
use Kanopi\Components\Model\Data\IStreamCollection;
use Kanopi\Components\Model\Exception\SetStreamException;

interface ISetStream {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param IStream $_input_stream
	 *
	 * @throws SetStreamException
	 *
	 * @return IStreamCollection
	 */
	function read( IStream $_input_stream ): IStreamCollection;
}