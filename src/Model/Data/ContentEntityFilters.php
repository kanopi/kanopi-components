<?php

namespace Kanopi\Components\Model\Data;

/**
 * Manage filters for content entity searches
 *  - Useful for CLI reports requesting different sets of metadata
 *
 * @package kanopi-components
 */
interface ContentEntityFilters {
	/**
	 * Subset associated meta keys
	 *
	 * @return array
	 */
	public function metaKeys(): array;

	/**
	 * Subset of content statuses
	 *
	 * @return array
	 */
	public function statuses(): array;

	/**
	 * Subset of associated taxonomies
	 *
	 * @return array
	 */
	public function taxonomies(): array;

	/**
	 * Subset of content types
	 *
	 * @return array
	 */
	public function types(): array;
}
