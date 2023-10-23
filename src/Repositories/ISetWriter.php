<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;

/**
 * Data writer interface for sets of indexed content in a target repository
 *
 * @package kanopi/components
 */
interface ISetWriter extends ISetReader {
	/**
	 * Create a new indexed entity in the repository
	 *    - Ideally returns an updated model with the repository generated identifier
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @return IIndexedEntity
	 * @throws SetWriterException Unable to create entity
	 */
	public function create( IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete a given indexed entity
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @return bool
	 * @throws SetWriterException Unable to delete entity
	 */
	public function delete( IIndexedEntity $_entity ): bool;

	/**
	 * Updates an indexed entity in the repository
	 *
	 * @param IIndexedEntity $_entity Entity model
	 *
	 * @return bool
	 * @throws SetWriterException Unable to update entity
	 */
	public function update( IIndexedEntity $_entity ): bool;
}
