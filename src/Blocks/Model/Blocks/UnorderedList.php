<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\ParentTransform;
use Kanopi\Components\Blocks\Model\RecursiveDomTransform;
use Kanopi\Components\Blocks\Model\StandardBlock;

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
