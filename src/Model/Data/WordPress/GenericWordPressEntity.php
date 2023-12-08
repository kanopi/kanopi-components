<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Transformers\Arrays;

/**
 * Generic Importer model to map Sharepoint ASPX content to WordPress posts
 *
 * @package kanopi-components
 */
class GenericWordPressEntity implements IPostTypeEntity {
	use PostTypeEntity;

	/**
	 * Build an entity
	 *  - Set default entities
	 */
	public function __construct() {
		$this->metaFields = Arrays::fresh();
		$this->taxonomies = Arrays::fresh();
	}

	/**
	 * Meta field mapping
	 *  - Values are all strings or null
	 *
	 * @var Arrays
	 */
	public Arrays $metaFields;
	/**
	 * Taxonomy to term mapping
	 *
	 * @var Arrays
	 */
	public Arrays $taxonomies;
	/**
	 * Optional post author, needed for new articles or to override existing authors
	 * - Only added to the written properties if non-null/non-zero
	 *
	 * @var int|null
	 */
	public ?int $postAuthor = null;

	/**
	 * {@inheritDoc}
	 */
	public function extraInsertFieldMapping(): array {
		return Arrays::fresh()
			->appendMaybe( [ 'post_author' => $this->postAuthor ], ! empty( $this->postAuthor ) )
			->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaFieldMapping(): array {
		return $this->metaFields->toArray();
	}

	/**
	 * Post last modified date
	 *
	 * @return string
	 */
	public function dateLastModified(): string {
		return $this->hasWPPost() ? $this->wpPost->post_modified : '';
	}

	/**
	 * Post published date
	 *
	 * @return string
	 */
	public function datePublished(): string {
		return $this->hasWPPost() ? $this->wpPost->post_date : '';
	}

	/**
	 * Proxy the post type for the articleType field, uses an underlying WP_Post post_type if empty
	 *
	 * @return string
	 */
	public function systemEntityName(): string {
		return $this->hasWPPost() ? $this->wpPost->post_type : 'unknown';
	}

	/**
	 * {@inheritDoc}
	 */
	public function taxonomyTermMapping(): array {
		$taxonomyTerms = Arrays::fresh();

		foreach ( $this->taxonomies->readSubArrays() as $taxonomy => $terms ) {
			$taxonomyTerms->writeIndex( $taxonomy, $terms );
		}

		return $taxonomyTerms->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifier(): string {
		return $this->indexIdentifier();
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->dateLastModified();
	}
}
