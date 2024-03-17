<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\StandardBlock;

/**
 * Transform an ul tag into a WordPress List Block with Inner List Item Blocks
 *
 * @package kanopi/components
 */
class UnorderedList implements RecursiveDomTransform {
	use StandardBlock;
	use ParentTransform;

	/**
	 * {@inheritDoc}
	 */
	protected function blockName(): string {
		return 'wp:list';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function blockTagName(): string {
		return 'ul';
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'ul';
	}
}
