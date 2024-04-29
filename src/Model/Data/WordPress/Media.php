<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Transformers\Arrays;

/**
 * WP REST API Generic Attachment/Media post type model
 *  - Requires the target system (being migrated into) attaches a post meta field named legacy_url to the attachment (Media) post type
 *
 * @package kanopi/components
 */
class Media implements MediaPostEntity {
	use PostTypeEntity;

	/**
	 * Generic media post type constructor
	 *
	 * @param string      $external_url      Legacy URL used as the cross-system unique identifier
	 * @param string      $post_mime_type    (Optional) Mime type of attachment, i.e. image/svg+xml, image/png
	 * @param string      $post_modified_gmt (Optional) Last Modification date/time in UTC
	 * @param int         $legacy_id         (Optional) Legacy ID from previous system, if available
	 * @param string|null $post_date_gmt     (Optional) Post date string from the remote source in UTC
	 * @param string|null $post_excerpt      (Optional) Caption, uses post excerpt
	 * @param string|null $post_name         (Optional) Desired post name/slug
	 * @param string      $_wp_attached_file (Optional) Attachment URL either relative to the Site URL or an external URL
	 * @param string      $overrideFileName  (Optional) Override the file name to write when importing
	 */
	public function __construct(
		public string $external_url = '',
		public string $post_mime_type = '',
		public string $post_modified_gmt = '',
		public ?int $legacy_id = null,
		private ?string $post_date_gmt = null,
		private ?string $post_excerpt = null,
		private ?string $post_name = null,
		private ?string $_wp_attached_file = null,
		private ?string $overrideFileName = null
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
	public function fileName(): string {
		$externalPath = $this->overrideFileName ?? $this->external_url;
		$path         = ! empty( $this->_wp_attached_file ) ? $this->_wp_attached_file : $externalPath;

		return pathinfo( $path, PATHINFO_BASENAME );
	}

	/**
	 * {@inheritDoc}
	 */
	public function legacyId(): ?string {
		return $this->legacy_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function externalUrl(): string {
		return $this->external_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaFieldMapping(): array {
		return $this->metaFields
			->writeIndex( 'external_url', $this->external_url )
			->appendMaybe( [ 'legacy_id' => $this->legacy_id ], null !== $this->legacy_id )
			->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function readMeta( string $_key ): mixed {
		return $this->metaFields->readIndex( $_key );
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
		return $this->external_url;
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

	/**
	 * {@inheritDoc}
	 */
	public function writeMeta( string $_key, mixed $_value ): MediaPostEntity {
		$this->metaFields->writeIndex( $_key, $_value );

		return $this;
	}
}
