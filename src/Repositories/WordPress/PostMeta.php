<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\ISetReader;

/**
 * Generic repository to read all post meta for a give post ID
 *
 * @package kanopi/components
 */
class PostMeta implements ISetReader {
	/**
	 * {@inheritDoc}
	 */
	public function read( $_filter = 0 ): EntityIterator {
		$meta = get_post_meta( $_filter );

		return new EntityIterator( false !== $meta ? $meta : [], 'array' );
	}
}
