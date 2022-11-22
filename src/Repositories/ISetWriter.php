<?php
/**
 * Data writer interface for sets of indexed content in a target repository
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;

interface ISetWriter extends ISetReader {
	/**
	 * Create a new indexed entity in the repository
	 *    - Ideally returns an updated model with the repository generated identifier
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @throws SetWriterException
	 *
	 * @return IIndexedEntity
	 */
	function create( IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete a given indexed entity
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @throws SetWriterException
	 *
	 * @return bool
	 */
	function delete( IIndexedEntity $_entity ): bool;

	/**
	 * Updates an indexed entity in the repository
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @throws SetWriterException
	 *
	 * @return bool
	 */
	function update( IIndexedEntity $_entity ): bool;
}