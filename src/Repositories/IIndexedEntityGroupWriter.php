<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetWriterException;

/**
 * Data writer interface for grouped entities to entities in a target repository
 *
 * @package kanopi/components
 */
interface IIndexedEntityGroupWriter extends IIndexedEntityGroupReader {
	/**
	 * Create a new group association to an indexed entity
	 *    - Ideally returns an updated model with the repository generated identifier
	 *
	 * @param int            $_identifier Entity identifier
	 * @param string         $_group_key  Group key name (taxonomy, etc.)
	 * @param EntityIterator $_entities   Entity set
	 *
	 * @return EntityIterator
	 * @throws SetWriterException Unable to create entity association
	 *
	 */
	public function create( int $_identifier, string $_group_key, EntityIterator $_entities ): EntityIterator;

	/**
	 * Delete a group association to an indexed entity
	 *
	 * @param int            $_identifier Entity identifier
	 * @param string         $_group_key  Group key name (taxonomy, etc.)
	 * @param EntityIterator $_entities   Entity set
	 *
	 * @return bool
	 * @throws SetWriterException Unable to delete association
	 *
	 */
	public function delete( int $_identifier, string $_group_key, EntityIterator $_entities ): bool;

	/**
	 * Updates a group association to an indexed entity
	 *
	 * @param int            $_identifier Entity identifier
	 * @param string         $_group_key  Group key name (taxonomy, etc.)
	 * @param EntityIterator $_entities   Entity set
	 *
	 * @return bool
	 * @throws SetWriterException Unable to update association
	 *
	 */
	public function update( int $_identifier, string $_group_key, EntityIterator $_entities ): bool;
}
