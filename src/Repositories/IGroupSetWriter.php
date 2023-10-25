<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;

/**
 * Data writer interface for grouped sets of indexed content in a target repository
 *    - For instance working with taxonomies in WordPress segmented by the taxonomy slug as group key
 *
 * @package kanopi/components
 */
interface IGroupSetWriter extends IGroupSetReader {
	/**
	 * Create a new indexed entity in the repository
	 *    - Ideally returns an updated model with the repository generated identifier
	 *
	 * @param string         $_group_key Group name
	 * @param IIndexedEntity $_entity    Entity model
	 *
	 * @return IIndexedEntity
	 * @throws SetWriterException Entity write failure
	 *
	 */
	public function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete a given indexed entity
	 *
	 * @param string         $_group_key Group name
	 * @param IIndexedEntity $_entity    Entity model
	 *
	 * @return boolean
	 * @throws SetWriterException Entity delete failure
	 *
	 */
	public function delete( string $_group_key, IIndexedEntity $_entity ): bool;

	/**
	 * Updates an indexed entity in the repository
	 *
	 * @param string         $_group_key Group name
	 * @param IIndexedEntity $_entity    Entity model
	 *
	 * @return boolean
	 * @throws SetWriterException Entity write failure
	 *
	 */
	public function update( string $_group_key, IIndexedEntity $_entity ): bool;
}
