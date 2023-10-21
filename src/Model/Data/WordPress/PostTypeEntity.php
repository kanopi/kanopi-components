<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Transformers\Arrays;
use Kanopi\Components\Transformers\Strings;
use WP_Post;

trait PostTypeEntity {
	/**
	 * Override system post content
	 *
	 * @var string|null
	 */
	protected ?string $_content = null;
	/**
	 * Override system post identifier
	 *
	 * @var int|null
	 */
	protected ?int $_postId = null;
	/**
	 * Override system post status
	 *
	 * @var string|null
	 */
	protected ?string $_status = null;
	/**
	 * Override system post title
	 *
	 * @var string|null
	 */
	protected ?string $_title = null;
	/**
	 * Wrapped WP_Post entity if read from the system
	 *
	 * @var WP_Post|null
	 */
	protected ?WP_Post $_wpPost = null;

	/**
	 * @see IPostTypeEntity::fromWPPost()
	 */
	static function fromWPPost( WP_Post $_wpPost ): IPostTypeEntity {
		return (new static())->updateWPPost( $_wpPost );
	}

	/**
	 * @see IPostTypeEntity::updateWPPost()
	 */
	function updateWPPost( ?WP_Post $_wpPost ): IPostTypeEntity {
		$this->_wpPost = $_wpPost;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see wp_insert_post
	 */
	function systemTransform(): array {
		return Arrays::from( [
			'post_status'  => $this->status(),
			'post_type'    => $this->systemEntityName(),
			'post_content' => $this->content(),
			'post_title'   => $this->title(),
		] )
			->appendMaybe( ['ID' => $this->indexIdentifier()], 0 < $this->indexIdentifier() )
			->appendMaybe( ['tax_input' => $this->taxonomyTermMapping()], !empty( $this->taxonomyTermMapping() ) )
			->appendMaybe( ['meta_input' => $this->metaFieldMapping()], !empty( $this->metaFieldMapping() ) )
			->appendMaybe( $this->extraInsertFieldMapping(), !empty( $this->extraInsertFieldMapping() ) )
			->filterUnique()
			->toArray();
	}

	/**
	 * @see IPostTypeEntity::status()
	 */
	function status(): string {
		return $this->_status ?? ($this->hasWPPost() ? $this->_wpPost->post_status : 'publish');
	}

	/**
	 * Human-readable term check
	 *
	 * @return bool
	 */
	protected function hasWPPost(): bool {
		return !empty( $this->_wpPost );
	}

	/**
	 * @see IIndexedEntity::systemEntityName()
	 */
	function systemEntityName(): string {
		$shortName = explode( '\\', static::class );
		return Strings::from( end( $shortName ) )->pascalToSeparate()->toString();
	}

	/**
	 * @see IPostTypeEntity::content()
	 */
	function content(): string {
		return $this->_content ?? ($this->hasWPPost() ? $this->_wpPost->post_content : '');
	}

	/**
	 * @see IPostTypeEntity::title()
	 */
	function title(): string {
		return $this->_title ?? ($this->hasWPPost() ? $this->_wpPost->post_title : '');
	}

	/**
	 * {@inheritDoc}
	 */
	function indexIdentifier(): int {
		return $this->_postId ?? ($this->hasWPPost() ? $this->_wpPost->ID : 0);
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 */
	function updateContent( string $_content ): IPostTypeEntity {
		$this->_content = $_content;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 */
	function updateIndexIdentifier( int $_index ): IPostTypeEntity {
		$this->_postId = $_index;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 */
	function updateStatus( string $_status ): IPostTypeEntity {
		$this->_status = $_status;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 */
	function updateTitle( string $_title ): IPostTypeEntity {
		$this->_title = $_title;

		return $this;
	}
}
