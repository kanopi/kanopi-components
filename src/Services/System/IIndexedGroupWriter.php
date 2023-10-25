<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\IIndexedGroupReader;

/**
 * Service interface to read/write grouped system entities
 *
 * @package kanopi/components
 */
interface IIndexedGroupWriter extends IIndexedGroupReader {
	/**
	 * Create a new entity
	 *
	 * @param string         $_group_key Group key
	 * @param IIndexedEntity $_entity    New system entity
	 *
	 * @return IIndexedEntity  Entity identifier with created index identifier
	 * @throws SetWriterException Unable to create system entity
	 */
	public function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete an entity of the given identifier
	 *
	 * @param string         $_group_key Group key
	 * @param IIndexedEntity $_entity    Existing entity to delete
	 *
	 * @return void
	 * @throws SetWriterException Unable to delete system entity
	 *
	 */
	public function delete( string $_group_key, IIndexedEntity $_entity ): void;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param string $_group_key        Group key
	 * @param int    $_index_identifier System index identifier
	 *
	 * @return ?IIndexedEntity
	 * @throws SetReaderException Unable to read system entity
	 */
	public function readByIndexIdentifier( string $_group_key, int $_index_identifier ): ?IIndexedEntity;

	/**
	 * Read a given Entity by shared Unique Identifier
	 *
	 * @param string $_group_key         Group key
	 * @param string $_unique_identifier Cross-system entity unique identifier
	 *
	 * @return ?IIndexedEntity
	 * @throws SetReaderException Unable to read system entity
	 */
	public function readByUniqueIdentifier( string $_group_key, string $_unique_identifier ): ?IIndexedEntity;

	/**
	 * Update an existing entity
	 *
	 * @param string         $_group_key Group key
	 * @param IIndexedEntity $_entity    Updated system entity
	 *
	 * @return bool Success of update
	 * @throws SetWriterException Unable to update system entity
	 *
	 */
	public function update( string $_group_key, IIndexedEntity $_entity ): bool;
}
