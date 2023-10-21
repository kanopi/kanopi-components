<?php

namespace Kanopi\Components\Logger;

/**
 * Verbose logging feature
 *
 * @package kanopi/components
 */
trait VerboseLogging {
	/**
	 * Whether verbose logging is enabled
	 * @var bool
	 */
	protected bool $verbose_enabled = false;

	/**
	 * Set verbose logging state
	 *
	 * @param bool $_is_enabled Whether verbose logging is enabled
	 *
	 * @see ILogger::enableVerboseLogging()
	 *
	 * @return void
	 */
	function enableVerboseLogging( bool $_is_enabled ): void {
		$this->verbose_enabled = $_is_enabled;
	}
}