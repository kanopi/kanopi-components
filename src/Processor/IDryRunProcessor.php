<?php

namespace Kanopi\Components\Processor;

interface IDryRunProcessor extends IImportStream {
	/**
	 * Use to enable Dry Run capability for the CLI process
	 *    - Dry run tests the processor without committing/making persistent data changes
	 *
	 * @param bool $_is_enabled
	 *
	 * @return void
	 */
	function enableDryRun( bool $_is_enabled ): void;

	/**
	 * Check the current Dry Run state
	 *
	 * @return bool
	 */
	function isDryRunEnabled(): bool;
}
