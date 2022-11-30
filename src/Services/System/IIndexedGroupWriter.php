<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Services\IIndexedGroupReader;

interface IIndexedGroupWriter extends IIndexedGroupReader {
	/**
	 * Create a new entity
	 *
	 * @param string         $_group_key
	 * @param IIndexedEntity $_entity
	 *
	 * @throws SetWriterException
	 *
	 * @return IIndexedEntity  Entity identifier with created index identifier
	 */
	function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity;

	/**
	 * Delete an entity of the given identifier
	 *
	 * @param string         $_group_key
	 * @param IIndexedEntity $_entity
	 *
	 * @throws
	 *
	 * @return void
	 */
	function delete( string $_group_key, IIndexedEntity $_entity ): void;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param string         $_group_key
	 * @param int $_index_identifier
	 *
	 * @throws SetReaderException
	 * @return ?IIndexedEntity
	 */
	function readByIndexIdentifier( string $_group_key, int $_index_identifier ): ?IIndexedEntity;

	/**
	 * Read a given Entity by shared Unique Identifier
	 *
	 * @param string         $_group_key
	 * @param string $_unique_identifier
	 *
	 * @throws SetReaderException
	 * @return ?IIndexedEntity
	 */
	function readByUniqueIdentifier( string $_group_key, string $_unique_identifier ): ?IIndexedEntity;

	/**
	 * Update an existing entity
	 *
	 * @param string         $_group_key
	 * @param IIndexedEntity $_entity
	 *
	 * @throws SetWriterException
	 *
	 * @return bool Success of update
	 */
	function update( string $_group_key, IIndexedEntity $_entity ): bool;
}