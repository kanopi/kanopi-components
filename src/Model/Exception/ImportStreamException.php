<?php
/**
 * Wrapper to catch ImportStream exceptions
 *
 * @package kanopi/utilities
 */

namespace Kanopi\Utilities\Model\Exception;

use Exception;
use Throwable;

class ImportStreamException extends Exception {
	/**
	 * @param string         $message
	 * @param int            $code
	 * @param Throwable|null $previous
	 */
	public function __construct( string $message = "", int $code = 0, ?Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return __CLASS__ . ": $this->message\n";
	}
}