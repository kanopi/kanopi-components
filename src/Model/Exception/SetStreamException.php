<?php

namespace Kanopi\Components\Model\Exception;

use Exception;

/**
 * Wrapper to catch SetStream transform exceptions
 *
 * @package kanopi/components
 */
class SetStreamException extends Exception {
	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}
