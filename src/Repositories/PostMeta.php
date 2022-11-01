<?php
/**
 * Generic repository to read all post meta for a give post ID
 */

namespace Kanopi\Utilities\Repositories;

use Kanopi\Utilities\Model\Collection\EntityIterator;

class PostMeta implements ISetReader {
	/**
	 * @inheritDoc
	 */
	function read( mixed $_filter = 0 ): EntityIterator {
		$meta = get_post_meta( $_filter );

		return new EntityIterator( false !== $meta ? $meta : [], 'array' );
	}
}