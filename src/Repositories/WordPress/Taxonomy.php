<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\WordPress\ITaxonomyTermEntity;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;
use Kanopi\Components\Transformers\Arrays;
use WP_Error;
use WP_Term;

/**
 * Generic repository interact with WordPress taxonomies and their terms
 *    - The read() $_filter is a proxy to the get_terms() argument array
 *
 * @package kanopi/components
 */
class Taxonomy implements IGroupSetWriter {
	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to create a taxonomy term
	 */
	public function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity {
		$term_name = is_a( $_entity, ITaxonomyTermEntity::class ) ? $_entity->name() : $_entity->uniqueIdentifier();
		$result    = wp_insert_term(
			$term_name,
			$_group_key,
			$_entity->systemTransform()
		);

		if ( is_a( $result, WP_Error::class ) ) {
			throw new SetWriterException(
				esc_html( Arrays::from( $result->get_error_messages() )->join( ' | ' ) )
			);
		}

		if ( empty( $result['term_id'] ) ) {
			throw new SetWriterException( 'Created term has no ID' );
		}

		return $_entity->updateIndexIdentifier( intval( $result['term_id'] ) );
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to delete a taxonomy term
	 */
	public function delete( string $_group_key, IIndexedEntity $_entity ): bool {
		$result = wp_delete_term( $_entity->indexIdentifier(), $_group_key );

		if ( is_a( $result, WP_Error::class ) ) {
			throw new SetWriterException(
				esc_html( Arrays::from( $result->get_error_messages() )->join( ' | ' ) )
			);
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function read( string $_group_key, $_filter = [] ): EntityIterator {
		$return_type = $_filter['fields'] ?? 'all';
		$entity_type = 'ids' === $return_type ? 'integer' : WP_Term::class;

		$taxonomy_query = Arrays::from(
			[
				'taxonomy' => $_group_key,
			]
		);
		$taxonomy_query->appendMaybe( $_filter, is_array( $_filter ) );
		$taxonomy_terms = get_terms( $taxonomy_query->toArray() );
		$isValid        = false !== $taxonomy_terms && ! is_a( $taxonomy_terms, WP_Error::class );

		return new EntityIterator( $isValid ? $taxonomy_terms : [], $entity_type );
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to update a taxonomy term
	 */
	public function update( string $_group_key, IIndexedEntity $_entity ): bool {
		$result = wp_update_term(
			$_entity->indexIdentifier(),
			$_group_key,
			$_entity->systemTransform()
		);

		if ( is_a( $result, WP_Error::class ) ) {
			throw new SetWriterException(
				esc_html( Arrays::from( $result->get_error_messages() )->join( ' | ' ) )
			);
		}

		return true;
	}
}
