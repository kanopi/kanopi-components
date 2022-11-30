<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Transformers\Arrays;

/**
 * Base indexed entity model for processing WordPress Taxonomy Terms
 *    To use:
 *      - Implement all remaining interface methods, mapping methods can return an empty array([]) if unused
 *      - System ID is set to 0, defaults for insert mode, it can be externally set with updateIndexIdentifier
 *      - post_content, post_status, and post_title are all required and set to defaults
 */
abstract class BaseTaxonomyTerm implements ITaxonomyTermEntity {
	use TaxonomyTermEntity;

	/**
	 * @inheritDoc
	 * @see wp_insert_post
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
}