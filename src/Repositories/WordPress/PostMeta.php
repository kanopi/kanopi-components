<?php
/**
 * Generic repository to read all post meta for a give post ID
 */

namespace Kanopi\Utilities\Repositories\WordPress;

use Kanopi\Utilities\Model\Collection\EntityIterator;
use Kanopi\Utilities\Repositories\ISetReader;

class PostMeta implements ISetReader {
	/**
	 * @inheritDoc
	 */
	function read( $_filter = 0 ): EntityIterator {
		$meta = get_post_meta( $_filter );

		return new EntityIterator( false !== $meta ? $meta : [], 'array' );
	}
}