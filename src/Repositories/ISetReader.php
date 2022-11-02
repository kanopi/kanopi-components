<?php
/**
 * Data reader interface for sets of content of a given type
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;

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