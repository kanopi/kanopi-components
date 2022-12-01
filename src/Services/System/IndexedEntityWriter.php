<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\Entities;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\ISetWriter;

trait IndexedEntityWriter {
	/**
	 * The internal entity set, from read() is an index of integers
	 */
	use Entities;

	/**
	 * System writable repository
	 *
	 * @returns ISetWriter
	 */
	abstract function entityRepository(): ISetWriter;

	/**
	 * Tracking flag tells if the location index is loaded
	 *
	 * @var bool
	 */
	protected bool $isIndexLoaded = false;

	/**
	 * Create a new entity
	 *
	 * @throws SetReaderException
	 * @throws SetWriterException
	 *
	 * @return IIndexedEntity Entity with created identifier
	 * @see IIndexedEntityWriter::create()
	 */
	function create( IIndexedEntity $_entity ): IIndexedEntity {
		$created_entity = $this->entityRepository()->create( $_entity );

		if ( !$this->hasEntityByIndex( $created_entity->indexIdentifier() ) ) {
			$this->entities->append( $created_entity->indexIdentifier() );
		}

		return $created_entity;
	}

	/**
	 * Delete an entity from the system
	 *
	 * @throws SetWriterException
	 * @see IIndexedEntityWriter::delete()
	 */
	function delete( IIndexedEntity $_entity ): void {
		$this->entityRepository()->delete( $_entity );
	}

	/**
	 * @param int $_index_identifier
	 *
	 * @throws SetReaderException
	 *
	 * @return bool
	 */
	protected function hasEntityByIndex( int $_index_identifier ): bool {
		return in_array( $_index_identifier, $this->read()->getArrayCopy(), true );
	}

	/**
	 * Stores and returns the currently indexed entity identifiers (not the models)
	 *
	 * @throws SetReaderException
	 * @return EntityIterator
	 * @see IIndexedEntityWriter::read()
	 */
	function read(): EntityIterator {
		if ( !$this->isIndexLoaded ) {
			$this->entities      = $this->entityRepository()->read( $this->readIndexFilter() );
			$this->isIndexLoaded = true;
		}

		return $this->entities;
	}

	/**
	 * Required repository query filter used to read the system index for the entity type
	 *    - IMPLEMENTATION NOTE: Used in read() to query a set of Index Identifiers (integers)
	 *
	 * @return mixed
	 */
	abstract function readIndexFilter();

	/**
	 * Update an existing entity
	 *
	 * @throws SetWriterException
	 *
	 * @return bool Success of update
	 * @see IIndexedEntityWriter::update()
	 */
	function update( IIndexedEntity $_entity ): bool {
		return $this->entityRepository()->update( $_entity );
	}
}