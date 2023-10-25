<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

/**
 * WordPress options table repository
 *
 * @package kanopi/components
 */
class Options implements IGroupSetWriter {
	/**
	 * {@inheritDoc}
	 * @throws SetWriterException Unable to write to option
	 */
	public function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity {
		$result = update_option( $_group_key, $_entity );

		if ( false === $result ) {
			throw new SetWriterException(
				esc_html( "Cannot update the option $_group_key" )
			);
		}

		return $_entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete( string $_group_key, IIndexedEntity $_entity ): bool {
		return delete_option( $_group_key );
	}

	/**
	 * {@inheritDoc}
	 */
	public function read( string $_group_key, $_filter = IIndexedEntity::class ): EntityIterator {
		$option = get_option( $_group_key );
		return new EntityIterator( ! empty( $option ) ? [ $option ] : [], IIndexedEntity::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function update( string $_group_key, IIndexedEntity $_entity ): bool {
		return update_option( $_group_key, $_entity );
	}
}
