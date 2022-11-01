<?php

namespace Kanopi\Utilities\Services;

use Kanopi\Utilities\Model\Collection\EntityIterator;

interface IIndexedEntityReader {
	/**
	 * Check to determine whether there is data to process
	 *
	 * @return bool
	 */
	function hasEntities(): bool;

	/**
	 * Read value/values for the underlying model
	 *
	 * @return EntityIterator
	 */
	function read(): EntityIterator;
}