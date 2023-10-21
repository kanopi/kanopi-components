<?php
/**
 * Generic repository to manage taxonomy terms for a given post
 *    - Automatically purges missing terms on create/update of associations
 */

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IIndexedEntityGroupWriter;
use WP_Error;
use WP_Term;

class PostTerms implements IIndexedEntityGroupWriter {
	/**
	 * {@inheritDoc}
	 */
	function delete( int $_identifier, string $_group_key, EntityIterator $_entities ): bool {
		$result = wp_remove_object_terms( $_identifier, $_entities->getArrayCopy(), $_group_key );

		return true === $result;
	}

	/**
	 * {@inheritDoc}
	 */
	function read( int $_identifier, string $_group_key ): EntityIterator {
		$terms = wp_get_object_terms( $_identifier, $_group_key );

		return new EntityIterator( false !== $terms ? $terms : [], WP_Term::class );
	}

	/**
	 * {@inheritDoc}
	 */
	function update( int $_identifier, string $_group_key, EntityIterator $_entities ): bool {
		$result = $this->create( $_identifier, $_group_key, $_entities );

		return !empty( $result );
	}

	/**
	 * {@inheritDoc}
	 */
	function create( int $_identifier, string $_group_key, EntityIterator $_entities ): EntityIterator {
		$terms = wp_set_object_terms( $_identifier, $_entities->getArrayCopy(), $_group_key, false );

		if (is_a( $terms, WP_Error::class )) {
			throw new SetWriterException( "Cannot add {$_group_key} terms for post {$_identifier}" );
		}

		return new EntityIterator( false !== $terms ? $terms : [], 'int' );
	}
}
