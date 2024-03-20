<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\ParentTransform;
use Kanopi\Components\Blocks\Model\RecursiveDomTransform;
use Kanopi\Components\Blocks\Model\StandardBlock;

/**
 * Transform a Paragraph tag into a WordPress Block
 *
 * @package kanopi/components
 */
class Paragraph implements RecursiveDomTransform {
	use ParentTransform;
	use StandardBlock;

	/**
	 * {@inheritDoc}
	 */
	protected function blockName(): string {
		return 'wp:paragraph';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function blockTagName(): string {
		return 'p';
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'p';
	}
}
