<?php
/**
 * Reader for a set of Indexed Entities in a group which is segmented in the system by a group key
 *    - For instance, WordPress taxonomies are grouped by taxonomy slug
 */

namespace Kanopi\Components\Services;

use Kanopi\Components\Model\Collection\EntityIterator;

interface IIndexedGroupReader {
	/**
	 * Check to determine whether there is data to process
	 *
	 * @param string $_group_key
	 *
	 * @return bool
	 */
	function hasEntities( string $_group_key ): bool;

	/**
	 * Read all value/values for the underlying model
	 *
	 * @param string $_group_key
	 *
	 * @return EntityIterator
	 */
	function read( string $_group_key ): EntityIterator;
}
