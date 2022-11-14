<?php
/**
 * String conversion utilities
 */

namespace Kanopi\Components\Converters;

class Strings {
	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * Strings constructor, wraps the subject for chainable operations
	 *
	 * @param string $_subject
	 */
	function __construct( string $_subject ) {
		$this->subject = $_subject;
	}

	/**
	 * Factory to build a strings converter
	 *
	 * @param string $_string
	 *
	 * @return Strings
	 */
	static function from( string $_string ) {
		return new Strings( $_string );
	}

	/**
	 * @param string $_separator - Optional, defaults to -
	 *
	 * @return Strings
	 */
	function pascal_to_separate( string $_separator = '-' ) {
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $this->subject, $matches);

		$conversion = $matches[0] ?? [ $this->subject ];

		foreach ( $conversion as &$match ) {
			$match = $match === strtoupper( $match ) ? strtolower( $match ) : lcfirst( $match );
		}

		return new Strings( implode( $_separator, $conversion ) );
	}

	/**
	 * @param string $_separator - Optional, defaults to -
	 *
	 * @return Strings
	 */
	function separate_to_pascal( string $_separator = '-' ) {
		return new Strings(
			implode(
				'',
				array_map( function( $_part ) {
					return ucfirst( $_part );
				}, explode( $_separator, $this->subject ) )
			)
		);
	}

	/**
	 * Retrieve the string
	 *
	 * @return string
	 */
	function to_string() {
		return $this->subject ?? '';
	}
}
