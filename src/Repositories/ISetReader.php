<?php
/**
 * Data reader interface for sets of indexed content to a target repository
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

interface ISetReader {
	/**
	 * Read a set of values with an optional filter
	 *
	 * @param mixed $_filter Optional filter for read operation
	 * @throws SetReaderException
	 *
	 * @return EntityIterator
	 */
	function read( $_filter = null ): EntityIterator;
}