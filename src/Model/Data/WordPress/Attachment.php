<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * Attachment post type class
 *
 * @package kanopi/components
 */
class Attachment implements IPostTypeEntity {
	use PostTypeEntity;

	// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore -- Allowing for internal file field
	/**
	 * Attachment URL either relative to the Site URL or an external URL
	 *
	 * @var string
	 */
	public string $_wp_attached_file = '';
	// phpcs:enable PSR2.Classes.PropertyDeclaration.Underscore -- Allowing for internal file field

	/**
	 * Base path for the attachment URL
	 *
	 * @var string
	 */
	protected string $basePath = '/';

	/**
	 * Change the base path used for the output of url()
	 *
	 * @param string $_path Base path
	 * @return void
	 */
	public function changeUrlBasePath( string $_path ): void {
		$this->basePath = str_ends_with( $_path, '/' ) ? $_path : ( $_path . '/' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function extraInsertFieldMapping(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaFieldMapping(): array {
		return [
			'_wp_attached_file' => $this->_wp_attached_file,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function taxonomyTermMapping(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifier(): string {
		return $this->_wp_attached_file;
	}

	/**
	 * Attachment URL is the combination of the base and attached file paths
	 *
	 * @return string
	 */
	public function url(): string {
		return $this->basePath . $this->_wp_attached_file;
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->_wpPost?->post_modified_gmt ?? '';
	}
}
