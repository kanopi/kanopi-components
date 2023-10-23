<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\ISetWriter;
use WP_Error;
use WP_Post;
use WP_Query;

/**
 * Generic post query retrieval, wraps WP_Query
 *
 * @package kanopi/components
 */
class PostQuery implements ISetWriter {
	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to create an entity
	 */
	public function create( IIndexedEntity $_entity ): IIndexedEntity {
		$post_id = wp_insert_post( $_entity->systemTransform() );
		if ( is_a( $post_id, WP_Error::class ) || 1 > $post_id ) {
			throw new SetWriterException(
				esc_html(
					"Unable to create entity of type {$_entity->systemEntityName()} "
					. "with Unique Identifier {$_entity->uniqueIdentifier()}"
				)
			);
		}

		return $_entity->updateIndexIdentifier( $post_id );
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to delete an entity
	 */
	public function delete( IIndexedEntity $_entity ): bool {
		$result = wp_delete_post( $_entity->indexIdentifier() );
		if ( empty( $result ) ) {
			throw new SetWriterException(
				esc_html(
					"Cannot delete entity of type {$_entity->systemEntityName()} "
					. "with Unique Identifier {$_entity->uniqueIdentifier()} "
					. "and Post Identifier {$_entity->indexIdentifier()}"
				)
			);
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function read( $_filter = [] ): EntityIterator {
		$return_type = $_filter['fields'] ?? 'all';
		$entity_type = 'ids' === $return_type ? 'integer' : WP_Post::class;
		return new EntityIterator( ( new WP_Query( $_filter ) )->posts, $entity_type );
	}

	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to update an entity
	 */
	public function update( IIndexedEntity $_entity ): bool {
		$post_id = wp_insert_post( $_entity->systemTransform() );
		if ( is_a( $post_id, WP_Error::class ) || 1 > $post_id ) {
			throw new SetWriterException(
				esc_html(
					"Unable to update entity of type {$_entity->systemEntityName()} "
					. "with Unique Identifier {$_entity->uniqueIdentifier()} "
					. "and Post Identifier {$_entity->indexIdentifier()}"
				)
			);
		}

		return true;
	}
}
