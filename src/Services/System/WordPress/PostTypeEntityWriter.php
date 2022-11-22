<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\IPostTypeEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\IIndexedEntityGroupWriter;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Services\System\IndexedEntityWriter;

trait PostTypeEntityWriter {
	use IndexedEntityWriter;

	/**
	 * Meta data repository
	 *
	 * @var ISetReader
	 */
	protected ISetReader $metaDataRepository;

	/**
	 * Taxonomy repository
	 *
	 * @var IIndexedEntityGroupWriter
	 */
	protected IIndexedEntityGroupWriter $taxonomyRepository;

	/**
	 * Maximum entity identifiers to retrieve during a read
	 *    - Override to change
	 *
	 * @return int
	 */
	protected function maximumIndexLength(): int {
		return 1000;
	}

	/**
	 * System name for the entity
	 *
	 * @return string
	 */
	abstract function systemEntityName(): string;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param int $_index_identifier
	 *
	 * @throws SetReaderException
	 * @return ?IPostTypeEntity
	 */
	function readByIndexIdentifier( int $_index_identifier ): ?IPostTypeEntity {
		return $this->hasEntityByIndex( $_index_identifier ) ? $this->readSystemEntity( $_index_identifier ) : null;
	}

	/**
	 * Implement this method to read a system entity with all meta fields and taxonomies
	 *
	 * @param int $_index_identifier
	 *
	 * @throws SetReaderException
	 * @return IPostTypeEntity|null
	 */
	abstract function readSystemEntity( int $_index_identifier ): ?IPostTypeEntity;

	/**
	 * Read an entity by a unique identifier
	 *    - Assumes the Unique Identifier is in a meta field named by uniqueIdentifierFieldName
	 *    - Override this function if it does not fit the use case
	 *
	 * @param string $_unique_identifier
	 *
	 * @throws SetReaderException
	 * @return IPostTypeEntity|null
	 */
	function readByUniqueIdentifier( string $_unique_identifier ): ?IPostTypeEntity {
		$post_id_cursor = $this->systemWriter->read( [
			'post_type'      => $this->systemEntityName(),
			// phpcs:ignore -- Intentional meta data query
			'meta_query'     => [
				[
					'key'   => $this->uniqueIdentifierFieldName(),
					'value' => $_unique_identifier
				]
			],
			'posts_per_page' => 1,
			'fields'         => 'ids'
		] );

		$post_id = $post_id_cursor->current();

		return null !== $post_id && $this->hasEntityByIndex( $post_id ) ? $this->readSystemEntity( $post_id ) : null;
	}

	/**
	 * @inheritDoc
	 */
	function readIndexFilter(): array {
		return [
			'post_type'      => $this->systemEntityName(),
			'posts_per_page' => $this->maximumIndexLength(),
			'fields'         => 'ids'
		];
	}

	/**
	 * Meta field name associated with the unique identifier
	 *
	 * @return string
	 */
	abstract function uniqueIdentifierFieldName(): string;
}