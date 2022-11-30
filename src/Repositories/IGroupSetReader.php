<?php
/**
 * Data reader interface for grouped sets of indexed content to a target repository
 * 	- For instance working with taxonomies in WordPress segmented by the taxonomy slug as group key
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

interface IGroupSetReader {
	/**
	 * Read a set of values with an optional filter
	 *
	 * @param string $_group_key
	 * @param mixed  $_filter Optional filter for read operation
	 *
	 * @throws SetReaderException
	 *
	 * @return EntityIterator
	 */
	function read( string $_group_key, $_filter = null ): EntityIterator;
}