<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Transformers\Arrays;

/**
 * WP REST API Generic Attachment/Media post type model
 *  - Requires the target system (being migrated into) attaches a post meta field named legacy_url to the attachment (Media) post type
 *
 * @package pen-content-migration
 */
class Media implements IPostTypeEntity {
	use PostTypeEntity;

	/**
	 * Generic post type constructor
	 *
	 * @param string      $post_mime_type    (Optional) Mime type of attachment, i.e. image/svg+xml, image/png
	 * @param string      $legacy_url        (Optional) Legacy URL used as the cross-system unique identifier
	 * @param int         $legacy_id         (Optional) Legacy ID from previous system, if available
	 * @param string      $post_modified_gmt (Optional) Last Modification date/time in UTC
	 * @param string|null $post_date_gmt     (Optional) Post date string from the remote source in UTC
	 * @param string|null $post_excerpt      (Optional) Caption, uses post excerpt
	 * @param string|null $post_name         (Optional) Desired post name/slug
	 * @param string      $_wp_attached_file (Optional) Attachment URL either relative to the Site URL or an external URL
	 */
	public function __construct(
		public string $post_mime_type = '',
		public string $legacy_url = '',
		public int $legacy_id = 0,
		public string $post_modified_gmt = '',
		private ?string $post_date_gmt = null,
		private ?string $post_excerpt = null,
		private ?string $post_name = null,
		private ?string $_wp_attached_file = null
	) {
		$this->metaFields = Arrays::fresh();
	}

	/**
	 * Meta field mapping
	 *  - Values are all strings or null
	 *
	 * @var Arrays
	 */
	public Arrays $metaFields;

	/**
	 * {@inheritDoc}
	 */
	public function extraInsertFieldMapping(): array {
		return Arrays::from(
			[
				'post_date_gmt'     => $this->post_date_gmt,
				'post_excerpt'      => $this->post_excerpt,
				'post_mime_type'    => $this->post_mime_type,
				'post_modified_gmt' => $this->post_modified_gmt,
			]
		)
		->appendMaybe( [ 'post_excerpt' => $this->caption() ], null !== $this->caption() )
		->appendMaybe( [ 'post_name' => $this->slug() ], null !== $this->slug() )
			->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function caption(): ?string {
		return $this->post_excerpt ?? ( $this->hasWPPost() ? $this->wpPost->post_excerpt : null );
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaFieldMapping(): array {
		return $this->metaFields
			->writeIndex( 'legacy_url', $this->legacy_url )
			->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function status(): string {
		return 'inherit';
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
	public function slug(): ?string {
		return $this->post_name ?? ( $this->hasWPPost() ? $this->wpPost->post_name : null );
	}

	/**
	 * {@inheritDoc}
	 *
	 * Attachments do not use taxonomies as of WP 6.5.2
	 */
	public function taxonomyTermMapping(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifier(): string {
		return $this->legacy_url;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Status is always 'inherit' for Attachments
	 */
	public function updateStatus( string $_status ): IPostTypeEntity {
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->post_modified_gmt;
	}
}
