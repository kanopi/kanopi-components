<?php

namespace Kanopi\Utilities\Services\System;

use Kanopi\Utilities\Model\Data\IAcmIndexedEntity;
use Kanopi\Utilities\Model\Exception\AcmWriterException;
use Kanopi\Utilities\Repositories\IAcmSetWriter;

trait AcmWriter {
	/**
	 * Base reader properties and methods
	 */
	use AcmReader;

	/**
	 * ACM write-capable repository
	 *
	 * @var IAcmSetWriter
	 */
	protected IAcmSetWriter $post_writer;

	/**
	 * Create a new entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return int New entity identifier
	 */
	function create( IAcmIndexedEntity $_entity ): int {
		$created_id = $this->post_writer->create( $_entity );

		if ( !$this->hasEntityByIndex( $created_id ) ) {
			$this->entities->append( $created_id );
		}

		return $created_id;
	}

	/**
	 * Update an existing entity
	 *
	 * @throws AcmWriterException
	 *
	 * @return bool Success of update
	 */
	function update( IAcmIndexedEntity $_entity ): bool {
		return $this->post_writer->update( $_entity );
	}
}