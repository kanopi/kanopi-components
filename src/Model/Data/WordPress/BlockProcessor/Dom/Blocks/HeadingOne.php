<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;

/**
 * Transform a h1 tag into a WordPress Heading Block with Level 1
 *
 * @package kanopi/components
 */
class HeadingOne implements RecursiveDomTransform {
	use Heading;

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'h1';
	}

	/**
	 * {@inheritDoc}
	 */
	public function headingTransformLevel(): int {
		return 1;
	}
}
