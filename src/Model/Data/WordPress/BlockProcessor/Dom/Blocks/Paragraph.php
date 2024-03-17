<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\StandardBlock;

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
