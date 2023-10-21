<?php

namespace Kanopi\Components\Processor;

trait DryRunProcessor {
	protected bool $_isDryRunEnabled = false;

	/**
	 * @param boolean $_is_enabled Next enabled state
	 *
	 * @return void
	 * @see IDryRunProcessor::enableDryRun()
	 */
	public function enableDryRun( bool $_is_enabled ): void {
		$this->_isDryRunEnabled = $_is_enabled;
	}

	/**
	 * @return boolean Current dry run state
	 * @see IDryRunProcessor::isDryRunEnabled()
	 */
	public function isDryRunEnabled(): bool {
		return $this->_isDryRunEnabled;
	}
}
