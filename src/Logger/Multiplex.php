<?php

namespace Kanopi\Components\Logger;

use Kanopi\Components\Model\Collection\EntityIterator;

class Multiplex implements ILogger {
	use VerboseLogging;

	protected EntityIterator $_loggers;

	public function __construct( array $_loggers = [] ) {
		$this->_loggers = new EntityIterator( $_loggers, ILogger::class );

		/**
		 * By default passes all logging onto each logger to determine if verbose logging is enabled
		 */
		$this->verbose_enabled = true;
	}

	/**
	 * @inheritDoc
	 */
	function error( string $_message ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ( $this->_loggers as $logger ) {
			$logger->error( $_message );
		}
	}

	/**
	 * @inheritDoc
	 */
	function info( string $_message ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ( $this->_loggers as $logger ) {
			$logger->info( $_message );
		}
	}

	/**
	 * @inheritDoc
	 */
	function table( array $_header, array $_messages ): void {
		/**
		 * @var ILogger $logger
		 */
		foreach ( $this->_loggers as $logger ) {
			$logger->table( $_header, $_messages );
		}
	}

	/**
	 * @inheritDoc
	 */
	function verbose( string $_message ): void {
		if ( $this->verbose_enabled ) {
			/**
			 * @var ILogger $logger
			 */
			foreach ( $this->_loggers as $logger ) {
				$logger->verbose( $_message );
			}
		}
	}
}