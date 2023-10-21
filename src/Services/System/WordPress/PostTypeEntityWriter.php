<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\IPostTypeEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Services\System\IndexedEntityWriter;
use WP_Post;

trait PostTypeEntityWriter {
	use IndexedEntityWriter;

	/**
	 * Read a given Entity by system index identifier
	 *
	 * @param int $_index_identifier
	 *
	 * @return ?IPostTypeEntity
	 * @throws SetReaderException
	 */
	function readByIndexIdentifier( int $_index_identifier ): ?IPostTypeEntity {
		$post_cursor = $this->entityRepository()->read( [
			'post_type'      => $this->systemEntityName(),
			'p'              => $_index_identifier,
			'posts_per_page' => 1,
			'fields'         => 'all',
		] );

		return $post_cursor->valid() ? $this->readSystemEntity( $post_cursor->current() ) : null;
	}

	/**
	 * System name for the entity
	 *
	 * @return string
	 */
	abstract function systemEntityName(): string;

	/**
	 * Implement this method to read a system entity with all meta fields and taxonomies
	 *    - Pass the WP_Post retrieved from a WP_Query to build the PostTypeEntity
	 *    - Use the Metadata and Taxonomy repositories to add additional data
	 *
	 * @param WP_Post $_post_entity
	 *
	 * @return IPostTypeEntity|null
	 * @throws SetReaderException
	 */
	abstract function readSystemEntity( WP_Post $_post_entity ): ?IPostTypeEntity;

	/**
	 * Read an entity by a unique identifier
	 *    - Assumes the Unique Identifier is in a meta field named by uniqueIdentifierFieldName
	 *    - Override this function if it does not fit the use case
	 *
	 * @param string $_unique_identifier
	 *
	 * @return IPostTypeEntity|null
	 * @throws SetReaderException
	 */
	function readByUniqueIdentifier( string $_unique_identifier ): ?IPostTypeEntity {
		$post_cursor = $this->entityRepository()->read( [
			'post_type'      => $this->systemEntityName(),
			// phpcs:ignore -- Intentional meta data query
			'meta_query'     => [
				[
					'key'   => $this->uniqueIdentifierFieldName(),
					'value' => $_unique_identifier,
				],
			],
			'posts_per_page' => 1,
			'fields'         => 'all',
		] );

		return $post_cursor->valid() ? $this->readSystemEntity( $post_cursor->current() ) : null;
	}

	/**
	 * Meta field name associated with the unique identifier
	 *
	 * @return string
	 */
	abstract function uniqueIdentifierFieldName(): string;

	/**
	 * {@inheritDoc}
	 * @see IndexedEntityWriter::readIndexFilter()
	 */
	function readIndexFilter(): array {
		return [
			'post_type'      => $this->systemEntityName(),
			'posts_per_page' => $this->maximumIndexLength(),
			'fields'         => 'ids',
		];
	}

	/**
	 * Maximum entity identifiers to retrieve during a read
	 *    - Index query returns IDs only, not full entities/posts
	 *  - Override to change
	 *
	 * @return int
	 */
	protected function maximumIndexLength(): int {
		return 10000;
	}
}
