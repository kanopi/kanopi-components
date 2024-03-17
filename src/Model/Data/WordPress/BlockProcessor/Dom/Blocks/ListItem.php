<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\StandardBlock;

/**
 * Transform a List Item tag into a WordPress List Item Block
 *
 * @package kanopi/components
 */
class ListItem implements RecursiveDomTransform {
	use StandardBlock;
	use ParentTransform;

	/**
	 * {@inheritDoc}
	 */
	protected function blockName(): string {
		return 'wp:list-item';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function blockTagName(): string {
		return 'li';
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'li';
	}
}
