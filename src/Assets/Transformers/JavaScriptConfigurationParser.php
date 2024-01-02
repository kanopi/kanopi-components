<?php

namespace Kanopi\Components\Assets\Transformers;

use Exception;
use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Model\Data\Stream\IStreamCollection;
use Kanopi\Components\Model\Data\Stream\StreamCollection;
use Kanopi\Components\Repositories\ISetStream;
use Kanopi\Components\Transformers\Arrays;

/**
 * Parse Kanopi Pack configuration from a JavaScript configuration file
 *  - Testing shows the initial read and parse of a configuration, independent of file type takes ~700-900 µs
 */
class JavaScriptConfigurationParser implements ISetStream {
	const REGEX_MODULES       = '/"?module\.exports"?\s*=\s*({((?>[^{}]*)|(?1))+})/';
	const REGEX_DEV_SERVERS   = '/"devServer"?\s*:\s*({((?>[^{}]*)|(?1))+})/';
	const REGEX_FILE_PATTERNS = '/"?filePatterns"?\s*:\s*({((?>[^{}]*)|(?1))+})/';

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
	 * Finds the set of recursively nested JSON-like values (wrapped by {}) for a give Token/Name
	 *  - Allows a different separator than :, to find JS variable assignments, like module.exports = {...}
	 *  - Use existing Regex patterns to run a bit faster than dynamic
	 *
	 * @param string $_content Content to parse
	 * @param string $_regex   Regex pattern to use
	 *
	 * @return string
	 */
	private function recursiveTokenMatches( string $_content, string $_regex ): string {
		preg_match( $_regex, $_content, $matches );

		return $matches[1] ?? '';
	}

	/**
	 * Read the contents of a given Configuration stream, for the asset properties
	 *  - Assumes the incoming stream is a Common JS file with standard module.exports, otherwise falls back to JSON
	 *  - The configuration sections `devServer` and `filePatterns` are read into sub-arrays
	 *  - Each sub-array contains either the configuration or a nested `error` sub-array if it cannot be read
	 *
	 * @param IStream $_input_stream Incoming file stream
	 *
	 * @return IStreamCollection
	 */
	public function read( IStream $_input_stream ): IStreamCollection {
		$tokens = $this->recursiveTokenMatches( $_input_stream->stream(), self::REGEX_MODULES );
		if ( empty( $tokens ) ) {
			$tokens = $_input_stream->stream();
		}

		$devServer     = $this->recursiveTokenMatches( $tokens, self::REGEX_DEV_SERVERS );
		$filePatterns  = $this->recursiveTokenMatches( $tokens, self::REGEX_FILE_PATTERNS );
		$configuration = Arrays::fresh()
			->writeIndex( 'devServer', $this->readJsonElements( $devServer ) )
			->writeIndex( 'filePatterns', $this->readJsonElements( $filePatterns ) )
			->toArray();

		return new StreamCollection( $configuration, $_input_stream );
	}
}
