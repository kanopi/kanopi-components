<?php
/**
 * Read a local file at a give path
 */

namespace Kanopi\Utilities\Repositories;

use InvalidArgumentException;

class LocalFileReader implements IStreamReader {
	/**
	 * @inheritDoc
	 */
	function read( string $_stream_path ): string {
		if ( false === file_exists( $_stream_path ) ) {
			throw new InvalidArgumentException(
				"Import file not found at: $_stream_path",
				'Import file does not exists' );
		}

		// phpcs:ignore -- File read is intentionally uncached, intended for singular read
		$contents = file_get_contents( $_stream_path );

		return !empty( $contents ) ? $contents : '';
	}
}