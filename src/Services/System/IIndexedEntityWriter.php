<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\IIndexedEntityReader;

/**
 * Service interface to read/write indexed entities
 *
 * @package kanopi/component
 */
interface IIndexedEntityWriter extends IIndexedEntityReader {
	/**
	 * Create a new entity
	 *
	 * @param IIndexedEntity $_entity New system entity
	 *
	 * @return IIndexedEntity  Entity identifier with created index identifier
	 *
	 * @throws SetWriterException Unable to create system entity
	 */
	public function create( IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete an entity of the given identifier
	 *
	 * @param IIndexedEntity $_entity Existing entity to delete
	 *
	 * @return void
	 *
	 * @throws SetWriterException Unable to delete system entity
	 */
	public function delete( IIndexedEntity $_entity ): void;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param int $_index_identifier System index identifier
	 *
	 * @return ?IIndexedEntity
	 * @throws SetReaderException Unable to read system entity
	 */
	public function readByIndexIdentifier( int $_index_identifier ): ?IIndexedEntity;

	/**
	 * Read a given Entity by shared Unique Identifier
	 *
	 * @param string $_unique_identifier System index identifier
	 *
	 * @return ?IIndexedEntity
	 * @throws SetReaderException Unable to read system entity
	 */
	public function readByUniqueIdentifier( string $_unique_identifier ): ?IIndexedEntity;

	/**
	 * Update an existing entity
	 *
	 * @param IIndexedEntity $_entity Updated system entity
	 *
	 * @return bool Success of update
	 *
	 * @throws SetWriterException Unable to update system entity
	 */
	public function update( IIndexedEntity $_entity ): bool;
}
