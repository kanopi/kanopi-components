<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\RecursiveDomTransform;

/**
 * Transform a h4 tag into a WordPress Heading Block with Level 4
 *
 * @package kanopi/components
 */
class HeadingFour implements RecursiveDomTransform {
	use Heading;

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'h4';
	}

	/**
	 * {@inheritDoc}
	 */
	public function headingTransformLevel(): int {
		return 4;
	}
}
