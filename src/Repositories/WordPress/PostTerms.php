<?php
/**
 * Generic repository to read all post taxonomy terms for a give post ID
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\WordPress\EntityTaxonomyFilter;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\ISetReader;
use WP_Term;

class PostTerms implements ISetReader {
	/**
	 * @inheritDoc
	 */
	function read( $_filter = null ): EntityIterator {
		if ( !is_a( $_filter, EntityTaxonomyFilter::class ) ) {
			throw new SetReaderException(
				'PostTerms repository requires a EntitiyTaxonomyFilter to read terms for a post' );
		}

		$terms = [];

		if ( $_filter->isValid() ?? false ) {
			$terms = wp_get_object_terms( $_filter->entity_id, $_filter->taxonomies );
		}

		return new EntityIterator( false !== $terms ? $terms : [], WP_Term::class );
	}
}