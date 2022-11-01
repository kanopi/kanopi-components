<?php
/**
 * Transform an iterable set into an EntityIterator
 */

namespace Kanopi\Utilities\Model\Transform;

use Kanopi\Utilities\Model\Collection\EntityIterator;

interface IEntitySet {
	/**
	 * Transform input set values to an entity set
	 *
	 * @param iterable $_input_set
	 *
	 * @return EntityIterator
	 */
	function transform( iterable $_input_set ): EntityIterator;
}