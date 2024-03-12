<?php

namespace Kanopi\Components\Processor;

/**
 * Trait for handling import stream Overwrite and Stop on Error states
 *
 * @package kanopi-components
 */
trait ProcessorStates {
	/**
	 * @var bool Whether to overwrite existing content during import.
	 */
	protected bool $overwriteContent = false;
	/**
	 * @var bool Whether to stop processing on error.
	 */
	protected bool $stopOnError = false;

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
	 * Whether to overwrite content even if newer
	 *
	 * @return bool
	 */
	public function willAlwaysOverwrite(): bool {
		return $this->overwriteContent;
	}

	/**
	 * Whether to stop on any error
	 *
	 * @return bool
	 */
	public function willStopOnError(): bool {
		return $this->stopOnError;
	}
}
