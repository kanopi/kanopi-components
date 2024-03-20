<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\RecursiveDomTransform;

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
