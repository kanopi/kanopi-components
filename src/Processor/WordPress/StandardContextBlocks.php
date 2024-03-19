<?php

namespace Kanopi\Components\Processor\WordPress;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;

/**
 * Combination interface to use for most Block conversion
 *
 * @package kanopi/components
 */
interface StandardContextBlocks extends BlockSiteContent, SingleContext, RecursiveDomTransform {
}
