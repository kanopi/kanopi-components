<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * Base indexed entity model for processing WordPress Taxonomy Terms
 *    To use:
 *      - Implement any remaining interface methods, mapping methods can return an empty array([]) if unused
 *      - System ID is set to 0, defaults for insert mode, it can be externally set with updateIndexIdentifier
 *        - Store a WP_Term object in the model and override individual properties for update operations
 */
abstract class BaseTaxonomyTerm implements ITaxonomyTermEntity {
	use TaxonomyTermEntity;
}
