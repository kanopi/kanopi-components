<?php

namespace Kanopi\Components\Model\Exception;

use Exception;

/**
 * Wrapper to catch SetStream read exceptions
 *
 * @package kanopi/components
 */
class SetReaderException extends Exception {
	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}
