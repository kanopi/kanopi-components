<?php

namespace Kanopi\Components\Services;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Reader for a set of Indexed Entities in a group which is segmented in the system by a group key
 *    - For instance, WordPress taxonomies are grouped by taxonomy slug
 *
 * @package kanopi/components
 */
interface IIndexedGroupReader {
	/**
	 * Check to determine whether there is data to process
	 *
	 * @param string $_group_key Group key
	 *
	 * @return bool
	 */
	public function hasEntities( string $_group_key ): bool;

	/**
	 * Read all value/values for the underlying model
	 *
	 * @param string $_group_key Group key
	 *
	 * @return EntityIterator
	 */
	public function read( string $_group_key ): EntityIterator;
}
