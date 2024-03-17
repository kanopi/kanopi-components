<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;

/**
 * Transform a h2 tag into a WordPress Heading Block with Level 2
 *
 * @package kanopi/components
 */
class HeadingTwo implements RecursiveDomTransform {
	use Heading;

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'h2';
	}

	/**
	 * {@inheritDoc}
	 */
	public function headingTransformLevel(): int {
		return 2;
	}
}
