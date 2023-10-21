<?php

namespace Kanopi\Components\Services;

use Kanopi\Components\Model\Collection\EntityIterator;

interface IIndexedEntityReader {
	/**
	 * Check to determine whether there is data to process
	 *
	 * @return bool
	 */
	function hasEntities(): bool;

	/**
	 * Read all value/values for the underlying model
	 *
	 * @return EntityIterator
	 */
	function read(): EntityIterator;
}
