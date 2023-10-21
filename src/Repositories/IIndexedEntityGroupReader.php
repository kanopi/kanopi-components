<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

/**
 * Data reader interface for grouped entities to entities in a target repository
 *
 * @package kanopi/components
 */
interface IIndexedEntityGroupReader {
	/**
	 * Read a set of values associated with an entity by group
	 *
	 * @param int    $_identifier Entity identifier
	 * @param string $_group_key  Group key name (taxonomy, etc.)
	 *
	 * @return EntityIterator
	 * @throws SetReaderException Unable to read entities
	 *
	 */
	public function read( int $_identifier, string $_group_key ): EntityIterator;
}
