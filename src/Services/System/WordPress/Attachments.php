<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\Attachment;
use Kanopi\Components\Model\Data\WordPress\IPostTypeEntity;
use Kanopi\Components\Repositories\IIndexedEntityGroupWriter;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Repositories\ISetWriter;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use WP_Post;

/**
 * WordPress standard attachment post type entity
 *
 * @package mpi-knbol
 */
class Attachments implements IIndexedEntityWriter {
	use PostTypeEntityWriter;
	use NamedPropertyMap;

	/**
	 * Constructor for PostType entity with added base file path
	 *  - Base file path is injected into attachments to form Attachment URLs
	 *
	 * @param ISetWriter                $entityRepository   Attachment/post entity repository
	 * @param ISetReader                $metaDataRepository Metadata entity repository
	 * @param IIndexedEntityGroupWriter $taxonomyRepository Entity term repository
	 * @param string                    $contentFilePath    Base file path for attached files
	 */
	public function __construct(
		protected ISetWriter $entityRepository,
		protected ISetReader $metaDataRepository,
		protected IIndexedEntityGroupWriter $taxonomyRepository,
		protected string $contentFilePath
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
	 * Set the base URL for attachments before mapping fields
	 *
	 * @param IPostTypeEntity $_entity Entity to process
	 *
	 * @return IPostTypeEntity
	 */
	protected function beforeEntityMapping( IPostTypeEntity $_entity ): IPostTypeEntity {
		if ( is_a( $_entity, Attachment::class ) ) {
			$_entity->changeUrlBasePath( $this->contentFilePath );
		}

		return $_entity;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function entityRepository(): ISetWriter {
		return $this->entityRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function metaDataRepository(): ISetReader {
		return $this->metaDataRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function readEntityClassName(): string {
		return Attachment::class;
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
		return '_wp_attached_file';
	}
}