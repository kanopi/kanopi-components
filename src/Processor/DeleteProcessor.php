<?php

namespace Kanopi\Components\Processor;

/**
 * Processor capability to remove system entities not found in the import stream
 *
 * @package kanopi/components
 */
interface DeleteProcessor extends IImportStream {
	/**
	 * Whether the processor should delete when complete
	 *
	 * @param bool $_isEnabled Next enabled state
	 *
	 * @return void
	 */
	public function changeDeleteStatus( bool $_isEnabled ): void;

	/**
	 * Check the current delete unprocessed state
	 *
	 * @return bool
	 */
	public function willDeleteUnprocessed(): bool;
}
