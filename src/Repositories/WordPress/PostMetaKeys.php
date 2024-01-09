<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\IIndexedEntityGroupReader;

/**
 * Generic repository to read post meta items by key
 *
 * @package kanopi/components
 */
class PostMetaKeys implements IIndexedEntityGroupReader {
	/**
	 * {@inheritDoc}
	 */
	public function read( int $_identifier, string $_group_key ): EntityIterator {
		$metaValue = get_post_meta( $_identifier, $_group_key );

		return new EntityIterator( is_array( $metaValue ) ? $metaValue : [], 'array' );
	}
}
