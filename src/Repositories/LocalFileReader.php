<?php

namespace Kanopi\Components\Repositories;

use InvalidArgumentException;
use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Model\Data\Stream\Stream;
use Kanopi\Components\Model\Data\Stream\StreamProperties;

/**
 * Read a local file at a give path
 *
 * @package kanopi/components
 */
class LocalFileReader implements IStreamReader {
	//phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- No actual output, exception messages
	/**
	 * {@inheritDoc}
	 * @throws InvalidArgumentException File not found
	 */
	public function read( string $_stream_path ): IStream {
		$lastModifiedTimestamp = filemtime( $_stream_path );
		if ( false === $lastModifiedTimestamp ) {
			throw new InvalidArgumentException(
				"Import file not found at: $_stream_path",
				'Import file does not exists'
			);
		}

		// phpcs:ignore -- File read is intentionally uncached, intended for singular read
		$contents     = file_get_contents( $_stream_path );
		$readContents = ! empty( $contents ) ? $contents : '';

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
