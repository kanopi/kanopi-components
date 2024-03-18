<?php

namespace Kanopi\Components\Processor\WordPress;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;

/**
 * Container process for standard block content using a single context
 *  -
 *
 * @package kanopi/components
 */
class StandardBlockContent implements RecursiveDomTransform, BlockSiteContent, SingleContext {
	use ContextBlockContent;
	use SingleCurrentContext;
}
