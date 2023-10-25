<?php

namespace Kanopi\Components\Transformers;

/**
 * Common string manipulation in a state machine with a fluent interface
 *
 * @package kanopi/components
 */
class Strings {
	/**
	 * @var string
	 */
	protected string $subject;

	/**
	 * Strings constructor, wraps the subject for chainable operations
	 *
	 * @param string $_subject Original subject
	 */
	public function __construct( string $_subject ) {
		$this->subject = $_subject;
	}

	/**
	 * Factory to build a strings converter
	 *
	 * @param string $_subject Original subject
	 *
	 * @return Strings
	 */
	public static function from( string $_subject ): Strings {
		return new Strings( $_subject );
	}

	/**
	 * @param string $_separator - Optional, defaults to -
	 *
	 * @return Strings
	 */
	public function pascalToSeparate( string $_separator = '-' ): Strings {
		preg_match_all( '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $this->subject, $matches );

		$conversion = $matches[0] ?? [ $this->subject ];

		foreach ( $conversion as &$match ) {
			$match = strtoupper( $match ) === $match ? strtolower( $match ) : lcfirst( $match );
		}

		return new Strings( implode( $_separator, $conversion ) );
	}

	/**
	 * @param string $_separator - Optional, defaults to -
	 *
	 * @return Strings
	 */
	public function separateToPascal( string $_separator = '-' ): Strings {
		return new Strings(
			implode(
				'',
				array_map(
					function ( $_part ) {
						return ucfirst( $_part );
					},
					explode( $_separator, $this->subject )
				)
			)
		);
	}

	/**
	 * Retrieve the string
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->subject ?? '';
	}
}
