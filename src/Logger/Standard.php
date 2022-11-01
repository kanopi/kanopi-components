<?php
/**
 * Standard echo and WP error logging
 */

namespace Kanopi\Utilities\Logger;

class Standard implements ILogger {
	/**
	 * @inheritDoc
	 */
	function error( string $_message ): void {
		error_log( $_message );
	}

	/**
	 * @inheritDoc
	 */
	function info( string $_message ): void {
		echo $_message;
	}
}