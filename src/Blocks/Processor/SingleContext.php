<?php

namespace Kanopi\Components\Blocks\Processor;

use Kanopi\Components\Blocks\Model\Contexts\ContextStatus;
use Kanopi\Components\Blocks\Model\TransformContext;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Process which maintains one outer transform context at a time
 *
 * @package kanopi/components
 */
interface SingleContext {
	/**
	 * Clears the current top-level context
	 *
	 * @return void
	 */
	public function clearCurrentContext(): void;

	/**
	 * Whether there is a currently assigned outer context
	 *
	 * @return bool
	 */
	public function hasCurrentContext(): bool;

	/**
	 * Process an existing context
	 *  - Add the current node content to the context
	 *
	 * @param Crawler $_node         Current node
	 * @param Arrays  $_outputStream Current output stream array
	 *
	 * @return ContextStatus Context status
	 */
	public function processCurrentContext( Crawler $_node, Arrays $_outputStream ): ContextStatus;

	/**
	 * Check if a node starts a new context, setting it as the current context
	 *
	 * @param Crawler $_node Current node
	 *
	 * @return ContextStatus Context status
	 */
	public function processStartContext( Crawler $_node ): ContextStatus;

	/**
	 * Register a context for internal use
	 *  - Contexts are processed in FIFO order (First In, First Out/processed)
	 *
	 * @param TransformContext $_transform Context to register
	 * @return void
	 */
	public function registerContext( TransformContext $_transform ): void;
}
