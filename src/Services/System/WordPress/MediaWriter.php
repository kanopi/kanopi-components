<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data;
use Kanopi\Components\Model\Data\WordPress\MediaPostEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\IIndexedEntityGroupWriter;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Repositories\ISetWriter;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * WordPress standard attachment post type entity
 *  - Unique Identifier match is via RegEx comparison, it works with both direct file name and expression
 *
 * @package kanopi/components
 */
class MediaWriter implements IIndexedEntityWriter, MediaFileWriter {
	use PostTypeEntityWriter;
	use NamedPropertyMap;

	/**
	 * Constructor for PostType entity with added base file path
	 *  - Base file path is injected into attachments to form Attachment URLs
	 *
	 * @param ISetWriter                $entityRepository   Attachment/post entity repository
	 * @param ISetReader                $metaDataRepository Metadata entity repository
	 * @param IIndexedEntityGroupWriter $taxonomyRepository Entity term repository
	 * @param ImageWriter               $mediaWriter        Media image file writer service
	 */
	public function __construct(
		protected ISetWriter $entityRepository,
		protected ISetReader $metaDataRepository,
		protected IIndexedEntityGroupWriter $taxonomyRepository,
		protected ImageWriter $mediaWriter
	) {}

	/**
	 * Allowed post statuses for index reading
	 *  - Attachments use inherit
	 *
	 * @return string[]
	 */
	public function allowedIndexPostStatus(): array {
		return [ 'inherit' ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function entityRepository(): ISetWriter {
		return $this->entityRepository;
	}

		/**
		 * {@inheritDoc}
		 */
	public function importFile( MediaPostEntity $_media ): int {
		return $this->mediaWriter->import( $_media );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function metaDataRepository(): ISetReader {
		return $this->metaDataRepository;
	}

	/**
	 * Read an entity by a unique identifier
	 *    - Assumes the Unique Identifier is in a meta field named by uniqueIdentifierFieldName
	 *    - Override this function if it does not fit the use case
	 *
	 * @param string $_unique_identifier Entity index identifier
	 *
	 * @return Data\WordPress\IPostTypeEntity|null
	 * @throws SetReaderException Unable to read system entity
	 */
	public function readByUniqueIdentifier( string $_unique_identifier ): ?Data\WordPress\IPostTypeEntity {
		$post_cursor = $this->entityRepository()->read(
			[
				'post_status'    => $this->allowedIndexPostStatus(),
				'post_type'      => $this->systemEntityName(),
				// phpcs:ignore -- Intentional meta query, Regexp to match full path or date-based path fragments
				'meta_query'     => [
					[
						'key'     => $this->uniqueIdentifierFieldName(),
						'value'   => $_unique_identifier,
						// phpcs:ignore WordPressVIPMinimum.Performance.RegexpCompare.compare_compare -- Intentional
						'compare' => 'REGEXP',
					],
				],
				'posts_per_page' => 1,
				'fields'         => 'all',
			]
		);

		return $post_cursor->valid() ? $this->readSystemEntity( $post_cursor->current() ) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function readEntityClassName(): string {
		return Data\WordPress\Media::class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function systemEntityName(): string {
		return 'attachment';
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifierFieldName(): string {
		return 'external_url';
	}
}
