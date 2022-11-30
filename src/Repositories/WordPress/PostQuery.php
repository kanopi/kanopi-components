<?php
/**
 * Generic post query retrieval, wraps WP_Query
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\ISetWriter;
use WP_Error;
use WP_Post;
use WP_Query;

class PostQuery implements ISetWriter {
	/**
	 * @inheritDoc
	 */
	function create( IIndexedEntity $_entity ): IIndexedEntity {
		$post_id = wp_insert_post( $_entity->systemTransform() );
		if ( is_a( $post_id, WP_Error::class ) || 1 > $post_id ) {
			throw new SetWriterException(
				"Unable to create entity of type {$_entity->systemEntityName()} "
				. "with Unique Identifier {$_entity->uniqueIdentifier()}"
			);
		}

		$_entity->updateIndexIdentifier( $post_id );
		return $_entity;
	}

	/**
	 * @inheritDoc
	 */
	function delete( IIndexedEntity $_entity ): bool {
		$result = wp_delete_post( $_entity->indexIdentifier() );
		if ( empty( $result ) ) {
			throw new SetWriterException(
				"Cannot delete entity of type {$_entity->systemEntityName()} "
				. "with Unique Identifier {$_entity->uniqueIdentifier()} "
				. "and Post Identifier {$_entity->indexIdentifier()}"
			);
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function read( $_filter = [] ): EntityIterator {
		$return_type = $_filter[ 'fields' ] ?? 'all';
		$entity_type = 'ids' === $return_type ? 'integer' : WP_Post::class;
		return new EntityIterator( ( new WP_Query( $_filter ) )->posts, $entity_type );
	}

	/**
	 * @inheritDoc
	 */
	function update( IIndexedEntity $_entity ): bool {
		$post_id = wp_insert_post( $_entity->systemTransform() );
		if ( is_a( $post_id, WP_Error::class ) || 1 > $post_id ) {
			throw new SetWriterException(
				"Unable to update entity of type {$_entity->systemEntityName()} "
				. "with Unique Identifier {$_entity->uniqueIdentifier()} "
				. "and Post Identifier {$_entity->indexIdentifier()}"
			);
		}

		return true;
	}
}