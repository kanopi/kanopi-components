<?php

namespace Kanopi\Components\Model\Exception;

use Exception;

/**
 * Wrapper to catch ImportStream exceptions
 *
 * @package kanopi/components
 */
class ImportStreamException extends Exception {
	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}
