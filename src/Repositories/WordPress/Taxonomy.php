<?php
/**
 * Generic repository to read all terms from a get_terms query, using $filter as the argument array
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\ISetReader;
use WP_Term;

class Taxonomy implements ISetReader {
	/**
	 * @inheritDoc
	 */
	function read( $_filter = 0 ): EntityIterator {
		$taxonomy_terms = get_terms( $_filter );

		return new EntityIterator( false !== $taxonomy_terms ? $taxonomy_terms : [], WP_Term::class );
	}
}