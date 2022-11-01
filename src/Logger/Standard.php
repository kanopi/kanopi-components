<?php
/**
 * Standard echo and error logging
 */

namespace Kanopi\Utilities\Logger;

class Standard implements ILogger {
	/**
	 * @inheritDoc
	 */
	function error( string $_message ): void {
		// phpcs:ignore -- Intentionally writes to the error log
		error_log( strip_tags( $_message ) );
	}

	/**
	 * @inheritDoc
	 */
	function info( string $_message ): void {
		// phpcs:ignore -- Console output only, doesn't need sophisticated sanitization
		echo strip_tags( $_message );
	}
}