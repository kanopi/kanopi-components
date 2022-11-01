<?php
/**
 * Standard echo and WP error logging
 */

namespace Kanopi\Utilities\Logger;

use WP_CLI;

class CLI implements ILogger {
	/**
	 * @inheritDoc
	 */
	function error( string $_message ): void {
		WP_CLI::error( $_message, false );
	}

	/**
	 * @inheritDoc
	 */
	function info( string $_message ): void {
		WP_CLI::log( $_message );
	}
}