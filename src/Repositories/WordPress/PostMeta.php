<?php
/**
 * Generic repository to read all post meta for a give post ID
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\ISetReader;

class PostMeta implements ISetReader {
	/**
	 * {@inheritDoc}
	 */
	function read( $_filter = 0 ): EntityIterator {
		$meta = get_post_meta( $_filter );

		return new EntityIterator( false !== $meta ? $meta : [], 'array' );
	}
}
