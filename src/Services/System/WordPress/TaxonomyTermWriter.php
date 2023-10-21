<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\WordPress\ITaxonomyTermEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\IndexedGroupWriter;

trait TaxonomyTermWriter {
	use IndexedGroupWriter;

	/**
	 * @see IIndexedEntityWriter::hasEntities()
	 */
	function hasEntities( string $_group_key ): bool {
		return !empty( $this->entityGroups[$_group_key] );
	}

	/**
	 * @throws SetReaderException
	 * @see IIndexedEntityWriter::readByIndexIdentifier()
	 *
	 */
	function readByIndexIdentifier( string $_group_key, int $_index_identifier ): ?IIndexedEntity {
		$term_cursor = $this->entityRepository()->read(
			$_group_key,
			[
				'fields'     => 'all',
				'include'    => [$_index_identifier],
				'number'     => 1,
				'hide_empty' => false,
			]
		);

		return $term_cursor->valid() ? $this->readTaxonomyTerm( $term_cursor->current() ) : null;
	}

	/**
	 * Transform the taxonomy term into an indexed term entity
	 *
	 * @param mixed $_term
	 *
	 * @return ITaxonomyTermEntity
	 */
	abstract function readTaxonomyTerm( $_term ): ITaxonomyTermEntity;

	/**
	 * @throws SetReaderException
	 * @see IIndexedEntityWriter::readByUniqueIdentifier()
	 *
	 */
	function readByUniqueIdentifier( string $_group_key, string $_unique_identifier ): ?IIndexedEntity {
		$term_cursor = $this->entityRepository()->read(
			$_group_key,
			[
				'fields'     => 'all',
				'slug'       => [$_unique_identifier],
				'number'     => 1,
				'hide_empty' => false,
			]
		);

		return $term_cursor->valid() ? $this->readTaxonomyTerm( $term_cursor->current() ) : null;
	}

	/**
	 * @see IndexedEntityWriter::readIndexFilter()
	 */
	function readIndexFilter(): array {
		return [
			'fields'     => 'ids',
			'hide_empty' => false,
		];
	}
}
