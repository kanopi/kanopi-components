<?php

namespace Kanopi\Components\Processor\WordPress;

/**
 * Process Site Content into structured block content
 *
 * @package kanopi/components
 */
interface BlockSiteContent {
	/**
	 * Process content
	 *
	 * @param string $_content Incoming DOM as a string
	 *
	 * @return string
	 */
	public function process( string $_content ): string;
}
