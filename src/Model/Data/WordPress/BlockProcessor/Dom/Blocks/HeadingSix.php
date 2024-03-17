<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;

/**
 * Transform a h6 tag into a WordPress Heading Block with Level 6
 *
 * @package kanopi/components
 */
class HeadingSix implements RecursiveDomTransform {
	use Heading;

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'h6';
	}

	/**
	 * {@inheritDoc}
	 */
	public function headingTransformLevel(): int {
		return 6;
	}
}
