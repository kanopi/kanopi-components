<?php

namespace Kanopi\Components\Logger\WordPress;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Logger\VerboseLogging;
use WP_CLI;
use WP_CLI\Utils;

/**
 * Standard echo and WP error logging
 *
 * @package kanopi/components
 */
class CLI implements ILogger {
	use VerboseLogging;

	/**
	 * {@inheritDoc}
	 */
	public function error( string $_message ): void {
		WP_CLI::error( $_message, false );
	}

	/**
	 * {@inheritDoc}
	 */
	public function table( array $_header, array $_messages ): void {
		Utils\format_items(
			'table',
			$_messages,
			$_header
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function verbose( string $_message ): void {
		if ( $this->verbose_enabled ) {
			$this->info( $_message );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function info( string $_message ): void {
		WP_CLI::log( $_message );
	}
}
