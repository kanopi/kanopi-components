<?php
/**
 * Logging Interface
 */

namespace Kanopi\Components\Logger;

interface ILogger {
	/**
	 * Method to enable verbose logging if the implementation supports it
	 *
	 * @param bool $_is_enabled
	 *
	 * @return void
	 */
	function enableVerboseLogging( bool $_is_enabled ): void;

	/**
	 * Log an error message
	 *
	 * @param string $_message
	 */
	function error( string $_message ): void;

	/**
	 * Log an informational message
	 *
	 * @param string $_message
	 */
	function info( string $_message ): void;

	/**
	 * Show a listing/table of messages, if the implementation allows
	 *
	 * @param array $_header Header row of labels
	 * @param array $_messages Set of messages/items
	 */
	function table( array $_header, array $_messages ): void;

	/**
	 * Log a verbose message, up to implementation to filter these
	 *
	 * @param string $_message
	 *
	 * @return void
	 */
	function verbose( string $_message ): void;
}