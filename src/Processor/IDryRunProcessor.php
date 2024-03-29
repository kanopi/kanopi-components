<?php

namespace Kanopi\Components\Processor;

/**
 * Import stream processing dry run/test controls
 *
 * @package kanopi/components
 */
interface IDryRunProcessor extends IImportStream {
	/**
	 * Use to enable Dry Run capability for the CLI process
	 *    - Dry run tests the processor without committing/making persistent data changes
	 *
	 * @param bool $_is_enabled Next enabled state
	 *
	 * @return void
	 */
	public function enableDryRun( bool $_is_enabled ): void;

	/**
	 * Check the current Dry Run state
	 *
	 * @return bool
	 */
	public function isDryRunEnabled(): bool;
}
