<?php

namespace Kanopi\Components\Logger;

trait VerboseLogging {
	protected bool $verbose_enabled = false;

	/**
	 * @see ILogger::enableVerboseLogging()
	 */
	function enableVerboseLogging( bool $_is_enabled ): void {
		$this->verbose_enabled = $_is_enabled;
	}
}