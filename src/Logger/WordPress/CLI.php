<?php
/**
 * Standard echo and WP error logging
 */

namespace Kanopi\Components\Logger\WordPress;

use Kanopi\Components\Logger\ILogger;
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