<?php
/**
 * Data writer interface for sets of content of a given type
 */

namespace Kanopi\Utilities\Repositories;

use Kanopi\Utilities\Model\Data\IAcmIndexedEntity;
use Kanopi\Utilities\Model\Exception\AcmWriterException;

interface IAcmSetWriter extends ISetReader {
	/**
	 * Create a new entity
	 *
	 * @param IAcmIndexedEntity $_entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return int New entity identifier
	 */
	function create( IAcmIndexedEntity $_entity ): int;

	/**
	 * Create a new entity
	 *
	 * @param int $_index_identifier
	 *
	 * @throws AcmWriterException
	 */
	function delete( int $_index_identifier ): void;

	/**
	 * Update an existing entity
	 *
	 * @param IAcmIndexedEntity $_entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return bool Success of update
	 */
	function update( IAcmIndexedEntity $_entity ): bool;
}