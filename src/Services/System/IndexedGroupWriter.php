<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

trait IndexedGroupWriter {
	/**
	 * System writable repository
	 *
	 * @returns IGroupSetWriter
	 */
	abstract function entityRepository(): IGroupSetWriter;

	/**
	 * @var array
	 */
	protected array $entityGroups = [];

	/**
	 * Tracking flag tells if the location index is loaded
	 *
	 * @var array
	 */
	protected array $isIndexLoaded = [];

	/**
	 * @throws SetReaderException
	 * @throws SetWriterException
	 *
	 * @see IIndexedGroupWriter::create()
	 */
	function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity {
		$created_entity = $this->entityRepository()->create( $_group_key, $_entity );

		if ( !$this->hasEntityByIndex( $_group_key, $created_entity->indexIdentifier() ) ) {
			$this->entityGroups[ $_group_key ]->append( $created_entity->indexIdentifier() );
		}

		return $created_entity;
	}

	/**
	 * Delete an entity from the system
	 *
	 * @throws SetWriterException
	 * @see IIndexedEntityWriter::delete()
	 */
	function delete( string $_group_key, IIndexedEntity $_entity ): void {
		$this->entityRepository()->delete( $_group_key, $_entity );
	}

	/**
	 * See if an identifier exists in a specified group
	 *
	 * @param string $_group_key
	 * @param int    $_index_identifier
	 *
	 * @throws SetReaderException
	 *
	 * @return bool
	 */
	protected function hasEntityByIndex( string $_group_key, int $_index_identifier ): bool {
		return in_array( $_index_identifier, $this->read( $_group_key )->getArrayCopy(), true );
	}


	/**
	 * Required repository query filter used to read the system index for the grouped entity type
	 *    - IMPLEMENTATION NOTE: Used in read() to query a set of Index Identifiers (integers)
	 *
	 * @return mixed
	 */
	abstract function readIndexFilter();

	/**
	 * Stores and returns the currently indexed entity identifiers (not the models)
	 *
	 * @throws SetReaderException
	 *
	 * @see IIndexedEntityWriter::read()
	 */
	function read( string $_group_key, $_filter = [] ): EntityIterator {
		if ( !empty( $this->isIndexLoaded[ $_group_key ] ) ) {
			$this->entityGroups[ $_group_key ]  = $this->entityRepository()->read( $_group_key, $this->readIndexFilter() );
			$this->isIndexLoaded[ $_group_key ] = true;
		}

		return $this->entityGroups[ $_group_key ];
	}

	/**
	 * Update an existing entity
	 *
	 * @throws SetWriterException
	 *
	 * @return bool Success of update
	 * @see IIndexedEntityWriter::update()
	 */
	function update( string $_group_key, IIndexedEntity $_entity ): bool {
		return $this->entityRepository()->update( $_group_key, $_entity );
	}
}