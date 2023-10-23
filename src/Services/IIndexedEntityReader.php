<?php

namespace Kanopi\Components\Services;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Service interface to read indexed entities
 *
 * @package kanopi/components
 */
interface IIndexedEntityReader {
	/**
	 * Check to determine whether there is data to process
	 *
	 * @return bool
	 */
	public function hasEntities(): bool;

	/**
	 * Read all value/values for the underlying model
	 *
	 * @return EntityIterator
	 */
	public function read(): EntityIterator;
}
