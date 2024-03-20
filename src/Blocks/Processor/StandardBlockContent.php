<?php

namespace Kanopi\Components\Blocks\Processor;

/**
 * Container process for standard block content using a single context
 *
 * @package kanopi/components
 */
class StandardBlockContent implements StandardContextBlocks {
	use ContextBlockContent;
	use SingleCurrentContext;
}
