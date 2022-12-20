<?php
/**
 * Convert a JSON string into a JSON array equivalent
 */

namespace Kanopi\Components\Repositories;

use ArrayIterator;
use EmptyIterator;
use Kanopi\Components\Model\Data\Stream\IStream;
use Kanopi\Components\Model\Data\Stream\IStreamCollection;
use Kanopi\Components\Model\Data\Stream\StreamCollection;
use Kanopi\Components\Model\Exception\SetStreamException;

class JsonSetStream implements ISetStream {
	const ERROR_REFERENCE = [
		JSON_ERROR_DEPTH            => 'The maximum stack depth has been exceeded.',
		JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON.',
		JSON_ERROR_CTRL_CHAR        => 'Control character error, possibly incorrectly encoded.',
		JSON_ERROR_SYNTAX           => 'Syntax error.',
		JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
		JSON_ERROR_RECURSION        => 'One or more recursive references in the value to be encoded.',
		JSON_ERROR_INF_OR_NAN       => 'One or more NAN or INF values in the value to be encoded.',
		JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
	];

	/**
	 * Translate the JSON string to an array
	 *
	 * @param string $_input
	 *
	 * @return iterable
	 */
	protected function process( string $_input ): iterable {
		$startSegment = 'efbbbf';
		$rawData      = ltrim( $_input, chr( 239 ) . chr( 187 ) . chr( 191 ) );
		$rawData      = $startSegment === substr( bin2hex( $rawData ), 0, strlen( $startSegment ) )
			? substr( $rawData, 3 )
			: $rawData;
		$data         = json_decode( $rawData, true );

		return !empty( $data ) && is_array( $data ) ? new ArrayIterator( $data ) : new EmptyIterator();
	}

	/**
	 * @inheritDoc
	 */
	public function read( IStream $_input_stream ): IStreamCollection {
		$output     = $this->process( $_input_stream->stream() ?? '' );
		$error_code = max( 0, json_last_error() );

		if ( 0 < $error_code ) {
			throw new SetStreamException(
				self::ERROR_REFERENCE[ $error_code ] ?? "Unknown JSON read error ($error_code)" );
		}

		return new StreamCollection( $output, $_input_stream );
	}
}