<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

/**
 * Data reader interface for sets of indexed content to a target repository
 *
 * @package kanopi/components
 */
interface ISetReader {
	//phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing -- Deliberate type version compatability
	/**
	 * Read a set of values with an optional filter
	 *
	 * @param mixed $_filter Optional filter for read operation
	 *
	 * @return EntityIterator
	 * @throws SetReaderException Unable to read entities
	 *
	 */
	public function read( $_filter = null ): EntityIterator;
}
