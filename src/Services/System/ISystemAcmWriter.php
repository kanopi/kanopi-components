<?php

namespace Kanopi\Utilities\Services\System;

use Kanopi\Utilities\Model\Data\IAcmIndexedEntity;
use Kanopi\Utilities\Model\Exception\AcmWriterException;
use Kanopi\Utilities\Services\IIndexedEntityReader;

interface ISystemAcmWriter extends IIndexedEntityReader {
	/**
	 * Create a new entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return int New entity identifier
	 */
	function create( IAcmIndexedEntity $_entity ): int;

	/**
	 * Delete an entity of the given identifier
	 *
	 * @param int $_index_identifier
	 *
	 * @throws
	 *
	 * @return void
	 */
	function delete( int $_index_identifier ): void;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param int $_index_identifier
	 *
	 * @return ?IAcmIndexedEntity
	 */
	function readByIndexIdentifier( int $_index_identifier ): ?IAcmIndexedEntity;

	/**
	 * Read a given Entity by shared Unique Identifier
	 *
	 * @param string $_unique_identifier
	 *
	 * @return ?IAcmIndexedEntity
	 */
	function readByUniqueIdentifier( string $_unique_identifier ): ?IAcmIndexedEntity;

	/**
	 * Update an existing entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return bool Success of update
	 */
	function update( IAcmIndexedEntity $_entity ): bool;
}