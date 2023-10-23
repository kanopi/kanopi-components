<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * Base indexed entity model for processing WordPress Post Types
 *
 * @deprecated Use the PostTypeEntity trait directly instead of an abstract
 *    To use:
 *      - Implement any remaining interface methods, mapping methods can return an empty array([]) if unused
 *      - System ID is set to 0, defaults for insert mode, it can be externally set with updateIndexIdentifier
 *        - Store a WP_Post object in the model and override individual properties for update operations
 *        - Any Metadata and Taxonomy properties are added as implemented class properties with appropriate types
 *        - Those properties are then mapped via the mapping methods for writing to WordPress
 */
abstract class BasePostType implements IPostTypeEntity {
	use PostTypeEntity;
}
