<?php

namespace Kanopi\Components\Processor;

/**
 * Common implementation for processor dry run/test controls
 *
 * @package kanopi/components
 */
trait DryRunProcessor {
	/**
	 * Whether dry run is enabled
	 *
	 * @var bool
	 */
	protected bool $dryRunEnabledFlag = false;

	/**
	 * @param boolean $_is_enabled Next enabled state
	 *
	 * @return void
	 * @see IDryRunProcessor::enableDryRun()
	 */
	public function enableDryRun( bool $_is_enabled ): void {
		$this->dryRunEnabledFlag = $_is_enabled;
	}

	/**
	 * @return boolean Current dry run state
	 * @see IDryRunProcessor::isDryRunEnabled()
	 */
	public function isDryRunEnabled(): bool {
		return $this->dryRunEnabledFlag;
	}
}
