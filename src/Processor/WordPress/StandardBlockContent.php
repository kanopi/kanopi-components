<?php

namespace Kanopi\Components\Processor\WordPress;

/**
 * Container process for standard block content using a single context
 *
 * @package kanopi/components
 */
class StandardBlockContent implements StandardContextBlocks {
	use ContextBlockContent;
	use SingleCurrentContext;
}
