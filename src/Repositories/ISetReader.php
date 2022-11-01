<?php
/**
 * Data reader interface for sets of content of a given type
 */

namespace Kanopi\Utilities\Repositories;

use Kanopi\Utilities\Model\Collection\EntityIterator;

interface ISetReader {
	/**
	 * Read value/values for the underlying model
	 *
	 * @param mixed $_filter Optional filter for read operation
	 *
	 * @return EntityIterator
	 */
	function read( $_filter = null ): EntityIterator;
}