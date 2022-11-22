<?php
/**
 * Data reader interface for grouped entities to entities in a target repository
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

interface IIndexedEntityGroupReader {
	/**
	 * Read a set of values associated with an entity by group
	 *
	 * @param int    $_identifier Entity identifier
	 * @param string $_group_key  Group key name (taxonomy, etc)
	 *
	 * @throws SetReaderException
	 *
	 * @return EntityIterator
	 */
	function read( int $_identifier, string $_group_key ): EntityIterator;
}