<?php

namespace Kanopi\Components\Assets\Services;

use Kanopi\Components\Repositories\IStreamReader;

/**
 * Read the Kanopi Pack configuration structure
 *
 * @package kanopi-components
 */
class ConfigurationReader {
	/**
	 * @var IStreamReader
	 */
	private IStreamReader $reader;

	/**
	 * Setup a ConfigurationReader service
	 *
	 * @param IStreamReader $_reader Configuration source reader
	 */
	public function __construct(
		IStreamReader $_reader
	) {
		$this->reader = $_reader;
	}

	/**
	 * Reads the current configuration
	 *
	 * @param string $_filePath Path to configuration file
	 * @return void
	 */
	public function read( string $_filePath ) {
	}
}
