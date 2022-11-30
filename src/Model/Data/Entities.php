<?php
/**
 * Common trait to hold a type-validated collection of entities
 */

namespace Kanopi\Components\Model\Data;

use Kanopi\Components\Model\Collection\EntityIterator;

trait Entities {
	/**
	 * Current/cached entity set
	 *
	 * @var EntityIterator
	 */
	protected EntityIterator $entities;

	/**
	 * See if set has any entities
	 *
	 * @return bool
	 */
	public function hasEntities(): bool {
		if ( empty( $this->entities ) ) {
			return false;
		}

		$this->entities->rewind();
		return $this->entities->valid();
	}

	/**
	 * Read the entity iterator
	 * 	- If empty/invalid, return an empty numeric iterator
	 *
	 * @return EntityIterator
	 */
	function read(): EntityIterator {
		return $this->hasEntities() ? $this->entities : new EntityIterator( [], 'int' );
	}
}