<?php

namespace Kanopi\Components\Model\Exception;

use Exception;

/**
 * Wrapper to catch exceptions from external Dependencies, like missing services
 *
 * @package kanopi/components
 */
class DependencyException extends Exception {
	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}
