<?php

namespace Kanopi\Components\Logger;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Logging container to forward messages to multiple loggers simultaneously
 *
 * @package kanopi/components
 */
class Multiplex implements ILogger {
	use VerboseLogging;
	
	/**
	 * Logger collection
	 *
	 * @var EntityIterator
	 */
	protected EntityIterator $_loggers;
	
	/**
	 * Multiplex constructor.
	 * 
	 * @param array $_loggers Set of initial loggers
	 */
	public function __construct( array $_loggers = [] ) {
		$this->_loggers = new EntityIterator( $_loggers, ILogger::class );
		
		/**
		 * By default passes all logging onto each logger to determine if verbose logging is enabled
		 */
		$this->verbose_enabled = true;
	}
	
	/**
	 * {@inheritDoc}
	 */
	function error( string $_message ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ($this->_loggers as $logger) {
			$logger->error( $_message );
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	function info( string $_message ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ($this->_loggers as $logger) {
			$logger->info( $_message );
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	function table( array $_header, array $_messages ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ($this->_loggers as $logger) {
			$logger->table( $_header, $_messages );
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	function verbose( string $_message ): void {
		if ($this->verbose_enabled) {
			/**
			 * @var ILogger $logger
			 */
			foreach ($this->_loggers as $logger) {
				$logger->verbose( $_message );
			}
		}
	}
}