<?php
/**
 * Data writer interface for grouped entities to entities in a target repository
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetWriterException;

interface IIndexedEntityGroupWriter extends IIndexedEntityGroupReader {
	/**
	 * Create a new group association to an indexed entity
	 *    - Ideally returns an updated model with the repository generated identifier
	 *
	 * @param int            $_identifier Entity identifier
	 * @param string         $_group_key  Group key name (taxonomy, etc)
	 * @param EntityIterator $_entities   Entity set
	 *
	 * @throws SetWriterException
	 *
	 * @return EntityIterator
	 */
	function create( int $_identifier, string $_group_key, EntityIterator $_entities ): EntityIterator;

	/**
	 * Delete a group association to an indexed entity
	 *
	 * @param int            $_identifier Entity identifier
	 * @param string         $_group_key  Group key name (taxonomy, etc)
	 * @param EntityIterator $_entities   Entity set
	 *
	 * @throws SetWriterException
	 *
	 * @return bool
	 */
	function delete( int $_identifier, string $_group_key, EntityIterator $_entities ): bool;

	/**
	 * Updates a group association to an indexed entity
	 *
	 * @param int            $_identifier     Entity identifier
	 * @param string         $_group_key      Group key name (taxonomy, etc)
	 * @param EntityIterator $_entities       Entity set
	 *
	 * @throws SetWriterException
	 *
	 * @return bool
	 */
	function update( int $_identifier, string $_group_key, EntityIterator $_entities ): bool;
}