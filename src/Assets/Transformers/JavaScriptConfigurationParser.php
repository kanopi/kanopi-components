<?php

namespace Kanopi\Components\Assets\Transformers;

use Exception;
use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Transformers\Arrays;

/**
 * Parse Kanopi Pack configuration from a JavaScript configuration file
 */
class JavaScriptConfigurationParser {
	/**
	 * Read an array of JSON elements from incoming content
	 *
	 * @param string $_content JSON content
	 *
	 * @return array
	 */
	private function readJsonElements( string $_content ): array {
		try {
			return json_decode( $_content, true, flags: JSON_THROW_ON_ERROR );
		} catch ( Exception $exception ) {
			return [ 'error' => $exception ];
		}
	}

	/**
	 * Read the contents of a given stream for the asset properties
	 *
	 * @param IStream $_fileStream Incoming file stream
	 *
	 * @return array
	 */
	public function read( IStream $_fileStream ): array {
		preg_match( '/devServer\"?:\s?({((?>[^{}]*)|(?1))+})/', $_fileStream->stream(), $devServer );
		preg_match( '/filePatterns\"?:\s?({((?>[^{}]*)|(?1))+})/', $_fileStream->stream(), $filePatterns );

		return Arrays::fresh()
			->writeIndex( 'devServer', $this->readJsonElements( $devServer[1] ?? '' ) )
			->writeIndex( 'filePatterns', $this->readJsonElements( $filePatterns[1] ?? '' ) )
			->toArray();
	}
}
