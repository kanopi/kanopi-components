<?php
/**
 * Common data set trait
 */

namespace Kanopi\Utilities\Model\Data;

use Kanopi\Utilities\Model\Collection\EntityIterator;

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
		$this->entities->rewind();
		return $this->entities->valid();
	}

	/**
	 * Read the entity iterator
	 *
	 * @return EntityIterator
	 */
	function read(): EntityIterator {
		return $this->entities;
	}
}