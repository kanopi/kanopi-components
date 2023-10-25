<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

/**
 * Common methods for service to read/write grouped system entities
 *
 * @package kanopi/components
 */
trait IndexedGroupWriter {
	/**
	 * @var array
	 */
	protected array $entityGroups = [];
	/**
	 * Associative array by group tracking flag tells if the groups index is loaded
	 *
	 * @var array
	 */
	protected array $isGroupIndexLoaded = [];

	/**
	 * {@inheritDoc}
	 * @throws SetReaderException Unable to check for existing system entities
	 *
	 * @see IIndexedGroupWriter::create()
	 */
	public function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity {
		$created_entity = $this->entityRepository()->create( $_group_key, $_entity );

		if ( ! $this->hasEntityByIndex( $_group_key, $created_entity->indexIdentifier() ) ) {
			$this->entityGroups[ $_group_key ]->append( $created_entity->indexIdentifier() );
		}

		return $created_entity;
	}

	/**
	 * System writable repository
	 *
	 * @returns IGroupSetWriter
	 */
	abstract public function entityRepository(): IGroupSetWriter;

	/**
	 * See if an identifier exists in a specified group
	 *
	 * @param string $_group_key        Group key
	 * @param int    $_index_identifier System index identifier
	 *
	 * @return bool
	 * @throws SetReaderException Unable to read system index
	 */
	protected function hasEntityByIndex( string $_group_key, int $_index_identifier ): bool {
		return in_array( $_index_identifier, $this->read( $_group_key )->getArrayCopy(), true );
	}

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- No filtering
	/**
	 * {@inheritDoc}
	 *
	 * @throws SetReaderException Unable to read system entity
	 *
	 * @see IIndexedEntityWriter::read()
	 */
	public function read( string $_group_key, $_filter = [] ): EntityIterator {
		if ( ! $this->isGroupIndexLoaded( $_group_key ) ) {
			$this->entityGroups[ $_group_key ] =
				$this->entityRepository()->read( $_group_key, $this->readIndexFilter() );
			$this->updateGroupIndexLoaded( $_group_key, true );
		}

		return $this->entityGroups[ $_group_key ];
	}
	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

	/**
	 * Readable check if an index is loaded for the given group
	 *
	 * @param string $_group_key Group key
	 *
	 * @return bool
	 */
	protected function isGroupIndexLoaded( string $_group_key ): bool {
		return ! empty( $this->isGroupIndexLoaded[ $_group_key ] );
	}

	/**
	 * Required repository query filter used to read the system index for the grouped entity type
	 *    - IMPLEMENTATION NOTE: Used in read() to query a set of Index Identifiers (integers)
	 *
	 * @return mixed
	 */
	abstract public function readIndexFilter(): mixed;

	/**
	 * Update the loaded state of the index for a given group
	 *
	 * @param string $_group_key Group key
	 * @param bool   $_is_loaded Whether the index is loaded
	 *
	 * @return void
	 */
	protected function updateGroupIndexLoaded( string $_group_key, bool $_is_loaded ): void {
		$this->isGroupIndexLoaded[ $_group_key ] = $_is_loaded;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetWriterException Unable to delete system entity
	 * @see IIndexedEntityWriter::delete()
	 */
	public function delete( string $_group_key, IIndexedEntity $_entity ): void {
		$this->entityRepository()->delete( $_group_key, $_entity );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetWriterException Unable to update system entity
	 *
	 * @see IIndexedEntityWriter::update()
	 */
	public function update( string $_group_key, IIndexedEntity $_entity ): bool {
		return $this->entityRepository()->update( $_group_key, $_entity );
	}
}
