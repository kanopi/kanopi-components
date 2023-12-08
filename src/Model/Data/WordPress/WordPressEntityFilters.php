<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\ContentEntityFilters;

/**
 * Content entity filters for WordPress
 *  - Useful for CLI reports requesting different sets of metadata
 *
 * @package kanopi-components
 */
class WordPressEntityFilters implements ContentEntityFilters {
	/**
	 * Set of requested metadata keys
	 *
	 * @var array
	 */
	private array $requestedMetaKeys;
	/**
	 * Set of requested post status slugs
	 *
	 * @var array
	 */
	private array $requestedPostStatuses;
	/**
	 * Set of requested post type slugs
	 *
	 * @var array
	 */
	private array $requestedPostTypes;
	/**
	 * Set of requested taxonomy slugs
	 *
	 * @var array
	 */
	private array $requestedTaxonomies;

	/**
	 * Content filter mapping
	 *
	 * @param array $_metaKeys   Set of requested meta keys
	 * @param array $_statuses   Set of requested statuses
	 * @param array $_taxonomies Set of requested taxonomies
	 * @param array $_types      Set of requested types
	 */
	public function __construct( array $_metaKeys, array $_statuses, array $_taxonomies, array $_types ) {
		$this->requestedMetaKeys     = $_metaKeys;
		$this->requestedPostStatuses = $_statuses;
		$this->requestedTaxonomies   = $_taxonomies;
		$this->requestedPostTypes    = $_types;
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaKeys(): array {
		return $this->requestedMetaKeys;
	}

	/**
	 * {@inheritDoc}
	 */
	public function statuses(): array {
		return $this->requestedPostStatuses;
	}

	/**
	 * @inheritDoc}
	 */
	public function taxonomies(): array {
		return $this->requestedTaxonomies;
	}

	/**
	 * {@inheritDoc}
	 */
	public function types(): array {
		return $this->requestedPostTypes;
	}
}
