<?php

namespace Kanopi\Components\Repositories\WordPress;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

class Options implements IGroupSetWriter {
	/**
	 * @inheritDoc
	 */
	function create( string $_group_key, IIndexedEntity $_entity ): IIndexedEntity {
		$result = update_option( $_group_key, $_entity );

		if ( false === $result ) {
			throw new SetWriterException( "Cannot update the option {$_group_key}" );
		}

		return $_entity;
	}

	/**
	 * @inheritDoc
	 */
	function delete( string $_group_key, IIndexedEntity $_entity ): bool {
		return delete_option( $_group_key );
	}

	/**
	 * Use $_filter to define the set of entities
	 *
	 * @inheritDoc
	 */
	function read( string $_group_key, $_filter = IIndexedEntity::class ): EntityIterator {
		$option = get_option( $_group_key );
		return new EntityIterator( !empty( $option ) ? [ $option ] : [], IIndexedEntity::class );
	}

	/**
	 * @inheritDoc
	 */
	function update( string $_group_key, IIndexedEntity $_entity ): bool {
		return update_option( $_group_key, $_entity );
	}
}
