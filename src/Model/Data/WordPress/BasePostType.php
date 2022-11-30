<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Transformers\Arrays;

/**
 * Base indexed entity model for processing WordPress Post Types
 *    To use:
 *      - Implement all remaining interface methods, mapping methods can return an empty array([]) if unused
 *      - System ID is set to 0, defaults for insert mode, it can be externally set with updateIndexIdentifier
 *      - post_content, post_status, and post_title are all required and set to defaults
 */
abstract class BasePostType implements IPostTypeEntity {
	use PostTypeEntity;

	/**
	 * @inheritDoc
	 * @see wp_insert_post
	 */
	function systemTransform(): array {
		return Arrays::from( [
			'post_status'  => $this->status(),
			'post_type'    => $this->systemEntityName(),
			'post_content' => $this->content(),
			'post_title'   => $this->title(),
		] )
			->appendMaybe( [ 'ID' => $this->indexIdentifier() ], 0 < $this->indexIdentifier() )
			->appendMaybe( [ 'tax_input' => $this->taxonomyTermMapping() ], !empty( $this->taxonomyTermMapping() ) )
			->appendMaybe( [ 'meta_input' => $this->metaFieldMapping() ], !empty( $this->metaFieldMapping() ) )
			->appendMaybe( $this->extraInsertFieldMapping(), !empty( $this->extraInsertFieldMapping() ) )
			->filterUnique()
			->toArray();
	}
}