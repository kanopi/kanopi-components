<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Transformers\Arrays;
use Kanopi\Components\Transformers\Strings;
use WP_Term;

/**
 * Starting implementation for the ITaxonomyTermEntity interface
 *
 * @package kanopi/components
 */
trait TaxonomyTermEntity {
	/**
	 * Override system term identifier
	 *
	 * @var int|null
	 */
	protected ?int $termId = null;
	/**
	 * Override system term description
	 *
	 * @var string|null
	 */
	protected ?string $description = null;
	/**
	 * Override system term name
	 *
	 * @var string|null
	 */
	protected ?string $name = null;
	/**
	 * System term parent identifier
	 *
	 * @var int|null
	 */
	protected ?int $parentId = null;
	/**
	 * Override term slug
	 *
	 * @var string|null
	 */
	protected ?string $slug = null;
	/**
	 * System term entity
	 *
	 * @var WP_Term|null
	 */
	protected ?WP_Term $wpTerm = null;

	/**
	 * @param WP_Term $_term WordPress term entity
	 *
	 * @see ITaxonomyTermEntity::fromWPTerm()
	 *
	 */
	public static function fromWPTerm( WP_Term $_term ): ITaxonomyTermEntity {
		return ( new static() )->updateWPTerm( $_term );
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeWPTerm()
	 */
	public function updateWPTerm( ?WP_Term $_term ): ITaxonomyTermEntity {
		$this->wpTerm = $_term;

		return $this;
	}

	/**
	 * Read the effective Index Identifier, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of 0
	 *
	 * @see IIndexedEntity::indexIdentifier()
	 */
	public function indexIdentifier(): int {
		return $this->termId ?? ( $this->hasWPTerm() ? $this->wpTerm->term_id : 0 );
	}

	/**
	 * Human-readable term check
	 *
	 * @return bool
	 */
	protected function hasWPTerm(): bool {
		return ! empty( $this->wpTerm );
	}

	/**
	 * @see IIndexedEntity::systemEntityName()
	 */
	public function systemEntityName(): string {
		$shortName = explode( '\\', static::class );
		return Strings::from( end( $shortName ) )->pascalToSeparate()->toString();
	}

	/**
	 * {@inheritDoc}
	 * @see wp_insert_term
	 * @see wp_update_term
	 */
	public function systemTransform(): array {
		return Arrays::from(
			[
				'description' => $this->description(),
				'name'        => $this->name(),
				'parent'      => $this->parentId(),
				'slug'        => $this->slug(),
			]
		)
			->filterUnique()
			->toArray();
	}

	/**
	 * Read the effective Term name, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of empty string
	 *
	 * @see ITaxonomyTermEntity::description()
	 */
	public function description(): string {
		return $this->description ?? ( $this->hasWPTerm() ? $this->wpTerm->description : '' );
	}

	/**
	 * Read the effective Term name, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of empty string
	 *
	 * @see ITaxonomyTermEntity::name()
	 */
	public function name(): string {
		return $this->name ?? ( $this->hasWPTerm() ? $this->wpTerm->name : '' );
	}

	/**
	 * Read the effective Parent ID, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of 0
	 *
	 * @see ITaxonomyTermEntity::parentId()
	 */
	public function parentId(): int {
		return $this->parentId ?? ( $this->hasWPTerm() ? $this->wpTerm->parent_id : 0 ) ?? 0;
	}

	/**
	 * Read the effective Slug, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of empty string
	 *
	 * @see ITaxonomyTermEntity::slug()
	 */
	public function slug(): string {
		return $this->slug ?? ( $this->hasWPTerm() ? $this->wpTerm->slug : '' );
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	public function updateDescription( string $_description ): ITaxonomyTermEntity {
		$this->description = $_description;

		return $this;
	}

	/**
	 * @param int $_index Term system identifier
	 *
	 * @see IIndexedEntity::updateIndexIdentifier()
	 *
	 */
	public function updateIndexIdentifier( int $_index ): IIndexedEntity {
		$this->termId = $_index;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	public function updateName( string $_name ): ITaxonomyTermEntity {
		$this->name = $_name;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	public function updateParentId( int $_parentId ): ITaxonomyTermEntity {
		$this->parentId = $_parentId;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	public function updateSlug( string $_slug ): ITaxonomyTermEntity {
		$this->slug = $_slug;

		return $this;
	}
}
