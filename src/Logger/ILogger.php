<?php

namespace Kanopi\Components\Logger;

/**
 * Logging Interface
 *
 * @package kanopi/components
 */
interface ILogger {
	/**
	 * Method to enable verbose logging if the implementation supports it
	 *
	 * @param boolean $_is_enabled Set logging enabled state
	 *
	 * @return void
	 */
	public function enableVerboseLogging( bool $_is_enabled ): void;

	/**
	 * Log an error message
	 *
	 * @param string $_message Message content
	 */
	public function error( string $_message ): void;

	/**
	 * Log an informational message
	 *
	 * @param string $_message Message content
	 */
	public function info( string $_message ): void;

	/**
	 * Show a listing/table of messages, if the implementation allows
	 *
	 * @param array $_header   Header row of labels
	 * @param array $_messages Set of messages/items
	 */
	public function table( array $_header, array $_messages ): void;

	/**
	 * Log a verbose message, up to implementation to filter these
	 *
	 * @param string $_message Message content
	 *
	 * @return void
	 */
	public function verbose( string $_message ): void;
}
