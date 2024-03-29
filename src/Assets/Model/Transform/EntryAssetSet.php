<?php

namespace Kanopi\Components\Assets\Model\Transform;

use Kanopi\Components\Assets\Model\EntryAsset;
use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Transformers\Arrays;

/**
 * Transform creates an EntityIterator of EntryAsset
 *
 * @package kanopi/components
 */
class EntryAssetSet implements IEntitySet {
	/**
	 * {@inheritDoc}
	 *
	 * Adds an index of the entry name to each EntryAsset model in the iterator
	 */
	public function transform( iterable $_input_set ): EntityIterator {
		$mapping = Arrays::fresh();

		foreach ( $_input_set as $entryName => $assets ) {
			$mapping->writeIndex( $entryName, EntryAsset::fromArray( $entryName, $assets ) );
		}

		return EntityIterator::fromArray( $mapping->toArray(), EntryAsset::class );
	}
}
