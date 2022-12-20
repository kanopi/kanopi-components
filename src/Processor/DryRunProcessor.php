<?php

namespace Kanopi\Components\Processor;

trait DryRunProcessor {
	protected bool $_isDryRunEnabled = false;

	/**
	 * @see IDryRunProcessor::enableDryRun()
	 */
	public function enableDryRun( bool $_is_enabled ): void {
		$this->_isDryRunEnabled = $_is_enabled;
	}

	/**
	 * @see IDryRunProcessor::isDryRunEnabled()
	 */
	public function isDryRunEnabled(): bool {
		return $this->_isDryRunEnabled;
	}
}
