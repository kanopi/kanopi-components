<?php

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetReaderException;

/**
 * Data reader interface for grouped sets of indexed content to a target repository
 *    - For instance working with taxonomies in WordPress segmented by the taxonomy slug as group key
 *
 * @package kanopi/components
 */
interface IGroupSetReader {
	//phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing -- Deliberate
	/**
	 * Read a set of values with an optional filter
	 *
	 * @param string $_group_key Group name
	 * @param mixed  $_filter    Optional filter for read operation
	 *
	 * @return EntityIterator
	 * @throws SetReaderException Grouped entity read exception
	 */
	function read( string $_group_key, $_filter = null ): EntityIterator;
}
