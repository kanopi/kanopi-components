<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\{ContentEntityFilters,
	IIndexedEntity,
	WordPress\GenericWordPressEntity,
	WordPress\IPostTypeEntity,
	WordPress\WordPressEntityFilters
};
use Kanopi\Components\Model\Exception\{SetReaderException, SetWriterException};
use Kanopi\Components\Repositories\{IIndexedEntityGroupReader, IIndexedEntityGroupWriter, ISetWriter};
use Kanopi\Components\Services\System\{DynamicFilters, IIndexedEntityWriter};
use Kanopi\Components\Transformers\Arrays;
use WP_Post;
use WP_Term;

/**
 * Site Content service to write new and changed content to WordPress for a given set of indexed content
 * - Combines content writing for all the sites post types
 * - Content is uniquely identified by the legacyUrl field
 * - The Content Index is set on creation and used to track a filter set of all site content
 *
 * @package kanopi-components
 */
class GenericWordPressEntityWriter implements IIndexedEntityWriter, DynamicFilters {
	use PostTypeEntityWriter;

	/**
	 * System entity repository
	 *
	 * @var ISetWriter
	 */
	private ISetWriter $entityRepository;
	/**
	 * Meta data repository
	 *
	 * @var IIndexedEntityGroupReader
	 */
	private IIndexedEntityGroupReader $metaDataRepository;
	/**
	 * Author ID used for new content
	 *
	 * @var int
	 */
	private int $newAuthorId;
	/**
	 * Taxonomy repository
	 *
	 * @var IIndexedEntityGroupWriter
	 */
	private IIndexedEntityGroupWriter $taxonomyRepository;
	/**
	 * Effective set of content filters
	 *
	 * @var ContentEntityFilters
	 */
	private ContentEntityFilters $filters;

	/**
	 * Base constructor deliberately requests all trait requested repositories
	 * Makes them available for implemented classes
	 *
	 * @param ISetWriter                $_entityRepository   System entity data interface
	 * @param IIndexedEntityGroupReader $_metaDataRepository Entity meta data interface
	 * @param IIndexedEntityGroupWriter $_taxonomyRepository Taxonomy term interface
	 * @param int                       $_newAuthorId        New content author system identifier
	 */
	public function __construct(
		ISetWriter $_entityRepository,
		IIndexedEntityGroupReader $_metaDataRepository,
		IIndexedEntityGroupWriter $_taxonomyRepository,
		int $_newAuthorId
	) {
		$this->entityRepository   = $_entityRepository;
		$this->metaDataRepository = $_metaDataRepository;
		$this->taxonomyRepository = $_taxonomyRepository;
		$this->newAuthorId        = $_newAuthorId;
		$this->changeFilters( new WordPressEntityFilters( [], [ 'publish', 'pending' ], [], [] ) );
	}

	/**
	 * @return ISetWriter
	 */
	public function entityRepository(): ISetWriter {
		return $this->entityRepository;
	}

	/**
	 * @param IIndexedEntity $_entity Entity to create
	 *
	 * @return IIndexedEntity
	 * @throws SetReaderException Failed to read entity from system
	 * @throws SetWriterException Failed to write entity to system
	 */
	public function create( IIndexedEntity $_entity ): IIndexedEntity {
		// Sets a user on all new content when available
		if ( 0 < $this->newAuthorId && is_a( $_entity, GenericWordPressEntity::class ) ) {
			$_entity->postAuthor = $this->newAuthorId;
		}

		$created = $this->entityRepository()->create( $_entity );

		if ( ! $this->hasEntityByIndex( $created->indexIdentifier() ) ) {
			$this->entities->append( $created->indexIdentifier() );
		}

		return $created;
	}

	/**
	 * {@inheritDoc}
	 */
	public function changeFilters( ContentEntityFilters $_filters ): DynamicFilters {
		$this->filters = $_filters;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function maximumIndexLength(): int {
		return 50000000;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readByIndexIdentifier( int $_index_identifier ): ?IPostTypeEntity {
		$post_cursor = $this->entityRepository()->read(
			[
				'post_type'      => $this->filters->types(),
				'post_status'    => $this->filters->statuses(),
				'p'              => $_index_identifier,
				'posts_per_page' => 1,
				'fields'         => 'all',
			]
		);

		return $post_cursor->valid() ? $this->readSystemEntity( $post_cursor->current() ) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readIndexFilter(): array {
		return [
			'post_type'      => $this->filters->types(),
			'post_status'    => $this->filters->statuses(),
			'posts_per_page' => $this->maximumIndexLength(),
			'fields'         => 'ids',
		];
	}

	/**
	 * @param WP_Post $_post_entity WordPress' system entity
	 *
	 * @throws SetReaderException Failed to read properties
	 * @return IPostTypeEntity|null
	 */
	public function readSystemEntity( WP_Post $_post_entity ): ?IPostTypeEntity {
		/**
		 * @var GenericWordPressEntity $contentEntity
		 */
		$contentEntity = GenericWordPressEntity::fromWPPost( $_post_entity );

		/**
		 * Read the full set of metadata, indexed by meta key
		 * Assumes all meta values are single entry strings, null otherwise
		 */
		foreach ( $this->filters->metaKeys() as $metaKey ) {
			$metaData = $this->metaDataRepository->read( $contentEntity->indexIdentifier(), $metaKey );
			$contentEntity->metaFields->writeIndex( $metaKey, $metaData->current() );
		}

		/**
		 * Read the taxonomy data associated with the entity
		 */
		foreach ( $this->filters->taxonomies() as $taxonomy ) {
			$terms = $this->taxonomyRepository->read( $contentEntity->indexIdentifier(), $taxonomy );

			/**
			 * @var WP_Term $_term
			 */
			$termList = Arrays::fresh();
			foreach ( $terms as $_term ) {
				$termList->writeIndex( $_term->slug, $_term );
			}

			$contentEntity->taxonomies->writeIndex( $taxonomy, $termList );
		}

		return $contentEntity;
	}

	/**
	 * @inheritDoc
	 */
	public function systemEntityName(): string {
		return 'any';
	}

	/**
	 * @inheritDoc
	 */
	public function uniqueIdentifierFieldName(): string {
		return 'ID';
	}

	/**
	 * @param IIndexedEntity $_entity Updated entity to write
	 *
	 * @return bool
	 * @throws SetWriterException Failed to write entity of taxonomy data
	 */
	public function update( IIndexedEntity $_entity ): bool {
		return $this->entityRepository()->update( $_entity );
	}
}
