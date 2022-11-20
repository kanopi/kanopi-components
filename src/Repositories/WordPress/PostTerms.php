<?php
/**
 * Generic repository to read all post taxonomy terms for a give post ID
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\WordPress\EntityTaxonomyFilter;
use Kanopi\Components\Repositories\ISetReader;
use WP_Term;

class PostTerms implements ISetReader {
	/**
	 * @inheritDoc
	 */
	function read( ?EntityTaxonomyFilter $_filter = null ): EntityIterator {
		$terms = [];

		if ( $_filter->isValid() ?? false ) {
			$terms = wp_get_object_terms( $_filter->entity_id, $_filter->taxonomies );
		}

		return new EntityIterator( false !== $terms ? $terms : [], WP_Term::class );
	}
}