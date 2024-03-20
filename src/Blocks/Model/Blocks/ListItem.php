<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\ParentTransform;
use Kanopi\Components\Blocks\Model\RecursiveDomTransform;
use Kanopi\Components\Blocks\Model\StandardBlock;

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
