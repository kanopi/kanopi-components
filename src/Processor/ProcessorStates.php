<?php

namespace Kanopi\Components\Processor;

/**
 * Trait for handling common import stream states
 *  - Delete (default true)
 *  - Overwrite (default false)
 *  - Stop on Error (default false)
 *
 * @package kanopi-components
 */
trait ProcessorStates {
	/**
	 * Whether to delete unprocessed on completion
	 *
	 * @var bool
	 */
	protected bool $deleteUnprocessed = true;
	/**
	 * Whether to overwrite existing content during import
	 *
	 * @var bool
	 */
	protected bool $overwriteContent = false;
	/**
	 * Whether to stop processing on error
	 *
	 * @var bool
	 */
	protected bool $stopOnError = false;

	/**
	 * Set the delete unprocessed status
	 *
	 * @param bool $_enableState Next overwrite state
	 */
	public function changeDeleteStatus( bool $_enableState ): void {
		$this->deleteUnprocessed = $_enableState;
	}

	/**
	 * Set the overwrite status
	 *
	 * @param bool $_enableState Next overwrite state
	 */
	public function changeOverwriteStatus( bool $_enableState ): void {
		$this->overwriteContent = $_enableState;
	}

	/**
	 * Set the stop on error status
	 *
	 * @param bool $_enableState Next enabled state
	 */
	public function changeStopOnError( bool $_enableState ): void {
		$this->stopOnError = $_enableState;
	}

	/**
	 * Whether to overwrite content even if newer (default false)
	 *
	 * @return bool
	 */
	public function willAlwaysOverwrite(): bool {
		return $this->overwriteContent;
	}

	/**
	 * Whether to delete unprocessed on completion (default false)
	 *
	 * @return bool
	 */
	public function willDeleteUnprocessed(): bool {
		return $this->deleteUnprocessed;
	}

	/**
	 * Whether to stop on any error (default false)
	 *
	 * @return bool
	 */
	public function willStopOnError(): bool {
		return $this->stopOnError;
	}
}
