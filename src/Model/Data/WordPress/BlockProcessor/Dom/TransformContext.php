<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Contexts\ContextStatus;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Context used to perform complex transformations across multiple DOM elements
 *
 * @package kanopi/components
 */
interface TransformContext extends DomTransform {
	/**
	 * Trigger to reset internal state, for instance when using for consecutive, separate pieces of content
	 *
	 * @return void
	 */
	public function reset(): void;

	/**
	 * Ends the current context, if the $_checkElement is the context end.
	 *
	 * @param Crawler $_checkElement Element to check for end
	 *
	 * @return ContextStatus Status of context after node processing
	 */
	public function tryEnd( Crawler $_checkElement ): ContextStatus;

	/**
	 * Starts the current context, if the $_checkElement is the context start.
	 *
	 * @param Crawler $_checkElement Element to check for start
	 *
	 * @return ContextStatus Status of context after node processing
	 */
	public function tryStart( Crawler $_checkElement ): ContextStatus;
}
