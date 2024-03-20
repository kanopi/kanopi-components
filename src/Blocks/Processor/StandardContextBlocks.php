<?php

namespace Kanopi\Components\Blocks\Processor;

use Kanopi\Components\Blocks\Model\RecursiveDomTransform;

/**
 * Combination interface to use for most Block conversion
 *
 * @package kanopi/components
 */
interface StandardContextBlocks extends BlockSiteContent, SingleContext, RecursiveDomTransform {
}
