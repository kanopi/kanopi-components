<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\IIndexedEntityReader;

interface IIndexedEntityWriter extends IIndexedEntityReader {
	/**
	 * Create a new entity
	 *
	 * @throws SetWriterException
	 *
	 * @return IIndexedEntity  Entity identifier with created index identifier
	 */
	function create( IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete an entity of the given identifier
	 *
	 * @param IIndexedEntity $_entity
	 *
	 * @throws
	 *
	 * @return void
	 */
	function delete( IIndexedEntity $_entity ): void;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param int $_index_identifier
	 *
	 * @throws SetReaderException
	 * @return ?IIndexedEntity
	 */
	function readByIndexIdentifier( int $_index_identifier ): ?IIndexedEntity;

	/**
	 * Read a given Entity by shared Unique Identifier
	 *
	 * @param string $_unique_identifier
	 *
	 * @throws SetReaderException
	 * @return ?IIndexedEntity
	 */
	function readByUniqueIdentifier( string $_unique_identifier ): ?IIndexedEntity;

	/**
	 * Update an existing entity
	 *
	 * @throws SetWriterException
	 *
	 * @return bool Success of update
	 */
	function update( IIndexedEntity $_entity ): bool;
}