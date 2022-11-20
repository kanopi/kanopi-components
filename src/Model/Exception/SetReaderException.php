<?php
/**
 * Wrapper to catch SetStream read exceptions
 *
 * @package kanopi/components
 */

namespace Kanopi\Components\Model\Exception;

use Exception;
use Throwable;

class SetReaderException extends Exception {
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