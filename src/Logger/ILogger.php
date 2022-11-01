<?php
/**
 * Logging Interface
 */

namespace Kanopi\Utilities\Logger;

interface ILogger {
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
}