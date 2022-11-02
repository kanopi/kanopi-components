<?php
/**
 * Generic post query retrieval, wraps WP_Query
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\ISetReader;
use WP_Post;
use WP_Query;

class PostQuery implements ISetReader {
	/**
	 * @inheritDoc
	 */
	public function read( $_filter = [] ): EntityIterator {
		$return_type = $_filter[ 'fields' ] ?? 'all';
		$entity_type = 'ids' === $return_type ? 'integer' : WP_Post::class;
		return new EntityIterator( ( new WP_Query( $_filter ) )->posts, $entity_type );
	}
}