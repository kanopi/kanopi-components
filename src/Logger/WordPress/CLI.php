<?php
/**
 * Standard echo and WP error logging
 */

namespace Kanopi\Utilities\Logger\WordPress;

use Kanopi\Utilities\Logger\ILogger;
use WP_CLI;

class CLI implements ILogger {
	/**
	 * @inheritDoc
	 */
	function error( $_message ): void {
		WP_CLI::error( $_message, false );
	}

	/**
	 * @inheritDoc
	 */
	function info( string $_message ): void {
		WP_CLI::log( $_message );
	}
}