<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\WordPress\ITaxonomyTermEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\IndexedGroupWriter;

/**
 * Common methods to read/write for taxonomy term repositories
 *
 * @package kanopi/components
 */
trait TaxonomyTermWriter {
	use IndexedGroupWriter;

	/**
	 * @param string $_group_key Term group key
	 *
	 * @see IIndexedEntityWriter::hasEntities()
	 *
	 */
	public function hasEntities( string $_group_key ): bool {
		return ! empty( $this->entityGroups[ $_group_key ] );
	}

	/**
	 * {@inheritDoc}
	 * @see IIndexedEntityWriter::readByIndexIdentifier()
	 *
	 * @throws SetReaderException Unable to read terms
	 */
	public function readByIndexIdentifier( string $_group_key, int $_index_identifier ): ?IIndexedEntity {
		$term_cursor = $this->entityRepository()->read(
			$_group_key,
			[
				'fields'     => 'all',
				'include'    => [ $_index_identifier ],
				'number'     => 1,
				'hide_empty' => false,
			]
		);

		return $term_cursor->valid() ? $this->readTaxonomyTerm( $term_cursor->current() ) : null;
	}

	/**
	 * Transform the taxonomy term into an indexed term entity
	 *
	 * @param mixed $_term Source term
	 *
	 * @return ITaxonomyTermEntity
	 */
	abstract public function readTaxonomyTerm( $_term ): ITaxonomyTermEntity;

	/**
	 * {@inheritDoc}
	 * @see IIndexedEntityWriter::readByUniqueIdentifier()
	 *
	 * @throws SetReaderException Unable to read terms
	 */
	public function readByUniqueIdentifier( string $_group_key, string $_unique_identifier ): ?IIndexedEntity {
		$term_cursor = $this->entityRepository()->read(
			$_group_key,
			[
				'fields'     => 'all',
				'slug'       => [ $_unique_identifier ],
				'number'     => 1,
				'hide_empty' => false,
			]
		);

		return $term_cursor->valid() ? $this->readTaxonomyTerm( $term_cursor->current() ) : null;
	}

	/**
	 * @see IndexedEntityWriter::readIndexFilter()
	 */
	public function readIndexFilter(): array {
		return [
			'fields'     => 'ids',
			'hide_empty' => false,
		];
	}
}
