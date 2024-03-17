<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\StandardBlock;

/**
 * Transform a Blockquote tag into a WordPress Quote Block
 *
 * @package kanopi/components
 */
class BlockQuote implements RecursiveDomTransform {
	use ParentTransform;
	use StandardBlock;

	/**
	 * @inheritDoc
	 */
	protected function blockName(): string {
		return 'wp:quote';
	}

	/**
	 * @inheritDoc
	 */
	protected function blockTagName(): string {
		return 'blockquote';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function defaultClass(): string {
		return 'wp-block-quote';
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'blockquote';
	}
}
