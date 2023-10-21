<?php

namespace Kanopi\Components\Model\Exception;

use Exception;

/**
 * Wrapper to catch SetStream exceptions
 *
 * @package kanopi/components
 */
class SetWriterException extends Exception {
	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}
