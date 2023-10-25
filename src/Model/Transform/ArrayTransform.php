<?php

namespace Kanopi\Components\Model\Transform;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Transformers\Arrays;

/**
 * Transform copies an iterable into EntityIterator of arrays
 *
 * @package kanopi/components
 */
class ArrayTransform implements IEntitySet {
	/**
	 * {@inheritDoc}
	 */
	public function transform( iterable $_input_set ): EntityIterator {
		$mapping = Arrays::from( [] );
		foreach ( $_input_set as $input ) {
			$mapping->append( [ $input ] );
		}

		return EntityIterator::fromArray( $mapping->toArray(), 'array' );
	}
}
