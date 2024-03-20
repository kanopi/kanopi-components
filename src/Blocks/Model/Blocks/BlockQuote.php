<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\ParentTransform;
use Kanopi\Components\Blocks\Model\RecursiveDomTransform;
use Kanopi\Components\Blocks\Model\StandardBlock;

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
