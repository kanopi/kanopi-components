<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\RecursiveDomTransform;

/**
 * Transform a h5 tag into a WordPress Heading Block with Level 5
 *
 * @package kanopi/components
 */
class HeadingFive implements RecursiveDomTransform {
	use Heading;

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'h5';
	}

	/**
	 * {@inheritDoc}
	 */
	public function headingTransformLevel(): int {
		return 5;
	}
}
