<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\Entities;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\ISetWriter;

/**
 * Common methods for a service to read/write indexed entities
 *
 * @package kanopi/component
 */
trait IndexedEntityWriter {
	/**
	 * The internal entity set, from read() is an index of integers
	 */
	use Entities;

	/**
	 * Tracking flag tells if the location index is loaded
	 *
	 * @var bool
	 */
	protected bool $isIndexLoaded = false;

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetReaderException Unable to create system entity
	 * @see IIndexedEntityWriter::create()
	 */
	public function create( IIndexedEntity $_entity ): IIndexedEntity {
		$created_entity = $this->entityRepository()->create( $_entity );

		if ( ! $this->hasEntityByIndex( $created_entity->indexIdentifier() ) ) {
			$this->entities->append( $created_entity->indexIdentifier() );
		}

		return $created_entity;
	}

	/**
	 * System writable repository
	 *
	 * @returns ISetWriter
	 */
	abstract public function entityRepository(): ISetWriter;

	/**
	 * Check if there is an existing entity with a given index
	 *
	 * @param int $_index_identifier System index identifier
	 *
	 * @return bool
	 * @throws SetReaderException Unable to read from system index
	 *
	 */
	protected function hasEntityByIndex( int $_index_identifier ): bool {
		return in_array( $_index_identifier, $this->read()->getArrayCopy(), true );
	}

	/**
	 * {@inheritDoc}
	 * @throws SetReaderException Unable to read from system index
	 *
	 * @see IIndexedEntityWriter::read()
	 */
	public function read(): EntityIterator {
		if ( ! $this->isIndexLoaded ) {
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
	abstract public function readIndexFilter();

	/**
	 * {@inheritDoc}
	 *
	 * @see IIndexedEntityWriter::delete()
	 */
	public function delete( IIndexedEntity $_entity ): void {
		$this->entityRepository()->delete( $_entity );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetWriterException Unable to update system entity
	 *
	 * @see IIndexedEntityWriter::update()
	 */
	public function update( IIndexedEntity $_entity ): bool {
		return $this->entityRepository()->update( $_entity );
	}
}
