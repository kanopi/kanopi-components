<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Transformers\Arrays;
use Kanopi\Components\Transformers\Strings;
use WP_Post;

/**
 * Starting implementation for the IPostTypeEntity interface
 *
 * @package kanopi/components
 */
trait PostTypeEntity {
	/**
	 * Override system post content
	 *
	 * @var string|null
	 */
	protected ?string $content = null;
	/**
	 * Override system post identifier
	 *
	 * @var int|null
	 */
	protected ?int $postId = null;
	/**
	 * Override system post status
	 *
	 * @var string|null
	 */
	protected ?string $status = null;
	/**
	 * Override system post title
	 *
	 * @var string|null
	 */
	protected ?string $title = null;
	/**
	 * Wrapped WP_Post entity if read from the system
	 *
	 * @var WP_Post|null
	 */
	protected ?WP_Post $wpPost = null;

	/**
	 * @see IPostTypeEntity::fromWPPost()
	 *
	 * @param WP_Post $_wpPost Source post entity
	 */
	public static function fromWPPost( WP_Post $_wpPost ): IPostTypeEntity {
		return ( new static() )->updateWPPost( $_wpPost );
	}

	/**
	 * @see IPostTypeEntity::updateWPPost()
	 *
	 * @param null|WP_Post $_wpPost Source post entity
	 */
	public function updateWPPost( ?WP_Post $_wpPost ): IPostTypeEntity {
		$this->wpPost = $_wpPost;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see wp_insert_post
	 */
	public function systemTransform(): array {
		return Arrays::from(
			[
				'post_status'  => $this->status(),
				'post_type'    => $this->systemEntityName(),
				'post_content' => $this->content(),
				'post_title'   => $this->title(),
			]
		)
			->appendMaybe( [ 'ID' => $this->indexIdentifier() ], 0 < $this->indexIdentifier() )
			->appendMaybe( [ 'tax_input' => $this->taxonomyTermMapping() ], ! empty( $this->taxonomyTermMapping() ) )
			->appendMaybe( [ 'meta_input' => $this->metaFieldMapping() ], ! empty( $this->metaFieldMapping() ) )
			->appendMaybe( $this->extraInsertFieldMapping(), ! empty( $this->extraInsertFieldMapping() ) )
			->filterUnique()
			->toArray();
	}

	/**
	 * @see IPostTypeEntity::status()
	 */
	public function status(): string {
		return $this->status ?? ( $this->hasWPPost() ? $this->wpPost->post_status : 'publish' );
	}

	/**
	 * Human-readable term check
	 *
	 * @return bool
	 */
	protected function hasWPPost(): bool {
		return ! empty( $this->wpPost );
	}

	/**
	 * Direct read access for the internal system entity (WP_Post)
	 *
	 * @return WP_Post|null
	 */
	public function internalSystemEntity(): ?WP_Post {
		return $this->wpPost;
	}

	/**
	 * @see IIndexedEntity::systemEntityName()
	 */
	public function systemEntityName(): string {
		$shortName = explode( '\\', static::class );
		return Strings::from( end( $shortName ) )->pascalToSeparate()->toString();
	}

	/**
	 * @see IPostTypeEntity::content()
	 */
	public function content(): string {
		return $this->content ?? ( $this->hasWPPost() ? $this->wpPost->post_content : '' );
	}

	/**
	 * @see IPostTypeEntity::title()
	 */
	public function title(): string {
		return $this->title ?? ( $this->hasWPPost() ? $this->wpPost->post_title : '' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function indexIdentifier(): int {
		return $this->postId ?? ( $this->hasWPPost() ? $this->wpPost->ID : 0 );
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 *
	 * @param string $_content New content
	 */
	public function updateContent( string $_content ): IPostTypeEntity {
		$this->content = $_content;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 *
	 * @param int $_index New index identifier
	 */
	public function updateIndexIdentifier( int $_index ): IPostTypeEntity {
		$this->postId = $_index;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 *
	 * @param string $_status New status
	 */
	public function updateStatus( string $_status ): IPostTypeEntity {
		$this->status = $_status;

		return $this;
	}

	/**
	 * @see IPostTypeEntity::updateIndexIdentifier()
	 *
	 * @param string $_title New title
	 */
	public function updateTitle( string $_title ): IPostTypeEntity {
		$this->title = $_title;

		return $this;
	}
}
