<?php
/**
 * Transform an iterable set into an EntityIterator
 */

namespace Kanopi\Components\Model\Transform;

use Kanopi\Components\Model\Collection\EntityIterator;

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