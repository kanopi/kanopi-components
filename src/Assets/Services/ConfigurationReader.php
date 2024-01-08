<?php

namespace Kanopi\Components\Assets\Services;

use Kanopi\Components\Assets\Model\Configuration;
use Kanopi\Components\Assets\Model\WebpackConfiguration;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Repositories\ISetStream;
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
	private IStreamReader $configurationReader;
	/**
	 * @var ISetStream
	 */
	private ISetStream $configurationParser;

	/**
	 * Set up a ConfigurationReader service
	 *
	 * @param IStreamReader $_configurationReader Configuration source reader
	 * @param ISetStream    $_configurationParser Configuration parser
	 */
	public function __construct(
		IStreamReader $_configurationReader,
		ISetStream $_configurationParser,
	) {
		$this->configurationReader = $_configurationReader;
		$this->configurationParser = $_configurationParser;
	}

	/**
	 * Reads the current configuration
	 *
	 * @param string $_filePath Path to configuration file
	 *
	 * @return Configuration
	 */
	public function read( string $_filePath ): Configuration {
		try {
			$rawConfiguration    = $this->configurationReader->read( $_filePath );
			$parsedConfiguration = $this->configurationParser->read( $rawConfiguration )->collection();
		} catch ( SetStreamException $exception ) {
			$parsedConfiguration = [
				'error' => $exception->getMessage(),
			];
		}

		return WebpackConfiguration::fromJson( $parsedConfiguration );
	}
}
