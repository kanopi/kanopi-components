<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Transformers\Arrays;
use Kanopi\Components\Transformers\Strings;
use WP_Term;

trait TaxonomyTermEntity {
	/**
	 * Override system term identifier
	 *
	 * @var int|null
	 */
	protected ?int $_termId = null;
	/**
	 * Override system term description
	 *
	 * @var string|null
	 */
	protected ?string $_description = null;
	/**
	 * Override system term name
	 *
	 * @var string|null
	 */
	protected ?string $_name = null;
	/**
	 * System term parent identifier
	 *
	 * @var int|null
	 */
	protected ?int $_parentId = null;
	/**
	 * Override term slug
	 *
	 * @var string|null
	 */
	protected ?string $_slug = null;
	/**
	 * System term entity
	 *
	 * @var WP_Term|null
	 */
	protected ?WP_Term $_wpTerm = null;

	/**
	 * @see ITaxonomyTermEntity::fromWPTerm()
	 */
	static function fromWPTerm( WP_Term $_term ): ITaxonomyTermEntity {
		return (new static())->updateWPTerm( $_term );
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeWPTerm()
	 */
	function updateWPTerm( ?WP_Term $_term ): ITaxonomyTermEntity {
		$this->_wpTerm = $_term;

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
	function indexIdentifier(): int {
		return $this->_termId ?? ($this->hasWPTerm() ? $this->_wpTerm->term_id : 0);
	}

	/**
	 * Human-readable term check
	 *
	 * @return bool
	 */
	protected function hasWPTerm(): bool {
		return !empty( $this->_wpTerm );
	}

	/**
	 * @see IIndexedEntity::systemEntityName()
	 */
	function systemEntityName(): string {
		$shortName = explode( '\\', static::class );
		return Strings::from( end( $shortName ) )->pascalToSeparate()->toString();
	}

	/**
	 * {@inheritDoc}
	 * @see wp_insert_term
	 * @see wp_update_term
	 */
	function systemTransform(): array {
		return Arrays::from( [
			'description' => $this->description(),
			'name'        => $this->name(),
			'parent'      => $this->parentId(),
			'slug'        => $this->slug(),
		] )
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
	function description(): string {
		return $this->_description ?? ($this->hasWPTerm() ? $this->_wpTerm->description : '');
	}

	/**
	 * Read the effective Term name, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of empty string
	 *
	 * @see ITaxonomyTermEntity::name()
	 */
	function name(): string {
		return $this->_name ?? ($this->hasWPTerm() ? $this->_wpTerm->name : '');
	}

	/**
	 * Read the effective Parent ID, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of 0
	 *
	 * @see ITaxonomyTermEntity::parentId()
	 */
	function parentId(): int {
		return $this->_parentId ?? ($this->hasWPTerm() ? $this->_wpTerm->parent_id : 0) ?? 0;
	}

	/**
	 * Read the effective Slug, in priority order:
	 *  - Checks for an override in the entity model
	 *  - Checks the underlying WP_Term
	 *  - Returns a default of empty string
	 *
	 * @see ITaxonomyTermEntity::slug()
	 */
	function slug(): string {
		return $this->_slug ?? ($this->hasWPTerm() ? $this->_wpTerm->slug : '');
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	function updateDescription( string $_description ): ITaxonomyTermEntity {
		$this->_description = $_description;

		return $this;
	}

	/**
	 * @see IIndexedEntity::updateIndexIdentifier()
	 */
	function updateIndexIdentifier( int $_index ): IIndexedEntity {
		$this->_termId = $_index;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	function updateName( string $_name ): ITaxonomyTermEntity {
		$this->_name = $_name;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	function updateParentId( int $_parentId ): ITaxonomyTermEntity {
		$this->_parentId = $_parentId;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see ITaxonomyTermEntity::changeDescription()
	 */
	function updateSlug( string $_slug ): ITaxonomyTermEntity {
		$this->_slug = $_slug;

		return $this;
	}
}
