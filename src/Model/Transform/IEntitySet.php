<?php

namespace Kanopi\Components\Model\Transform;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Transform an iterable set into an EntityIterator
 *
 * @package kanopi/components
 */
interface IEntitySet {
	/**
	 * Transform input set values to an entity set
	 *
	 * @param iterable $_input_set Iterable source set
	 *
	 * @return EntityIterator
	 */
	public function transform( iterable $_input_set ): EntityIterator;
}
