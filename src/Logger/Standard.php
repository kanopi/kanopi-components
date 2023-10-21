<?php

namespace Kanopi\Components\Logger;

/**
 * Standard echo and error logging
 *
 * @package kanopi/components
 */
class Standard implements ILogger {
	use VerboseLogging;

	/**
	 * {@inheritDoc}
	 */
	function error( string $_message ): void {
		// phpcs:ignore -- Intentionally writes to the error log
		error_log( strip_tags( $_message ) );
	}

	/**
	 * {@inheritDoc}
	 */
	function info( string $_message ): void {
		// phpcs:ignore -- Console output only, doesn't need sophisticated sanitization
		echo strip_tags( $_message );
	}

	/**
	 * {@inheritDoc}
	 */
	function table( array $_header, array $_messages ): void {
		if ( $this->verbose_enabled ) {
			// phpcs:ignore -- Console output only, doesn't need sophisticated sanitization
			print_r( $_header );
			// phpcs:ignore -- Console output only, doesn't need sophisticated sanitization
			print_r( $_messages );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	function verbose( string $_message ): void {
		if ( $this->verbose_enabled ) {
			$this->info( $_message );
		}
	}
}