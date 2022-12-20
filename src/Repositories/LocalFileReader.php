<?php
/**
 * Read a local file at a give path
 */

namespace Kanopi\Components\Repositories;

use InvalidArgumentException;
use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Model\Data\Stream\Stream;
use Kanopi\Components\Model\Data\Stream\StreamProperties;

class LocalFileReader implements IStreamReader {
	/**
	 * @inheritDoc
	 */
	function read( string $_stream_path ): IStream {
		$lastModifiedTimestamp = filemtime( $_stream_path );
		if ( false === $lastModifiedTimestamp ) {
			throw new InvalidArgumentException(
				"Import file not found at: $_stream_path",
				'Import file does not exists' );
		}

		// phpcs:ignore -- File read is intentionally uncached, intended for singular read
		$contents = file_get_contents( $_stream_path );
		$readContents = !empty( $contents ) ? $contents : '';

		return new Stream(
			$readContents,
			new StreamProperties(
				$_stream_path,
				$lastModifiedTimestamp,
				strlen( $readContents ),
				time()
			)
		);
	}
}