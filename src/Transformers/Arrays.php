<?php

namespace Kanopi\Components\Transformers;

/**
 * Common array manipulation in a state machine with a fluent interface
 *
 * @package kanopi/components
 */
class Arrays {
	/**
	 * Current inner state
	 *
	 * @var array
	 */
	protected array $subject;

	/**
	 * Arrays constructor, wraps the subject for chainable operations
	 *
	 * @param array $_subject Initial array
	 */
	public function __construct( array $_subject ) {
		$this->subject = $_subject;
	}

	/**
	 * Factory to build an Arrays structure
	 *
	 * @param array $_subject Initial array
	 *
	 * @return Arrays
	 */
	public static function from( array $_subject ): Arrays {
		return new Arrays( $_subject );
	}

	/**
	 * Append an array segment to the inner subject
	 *
	 * @param array $_addition Array segment to append
	 *
	 * @return Arrays
	 */
	public function append( array $_addition ): Arrays {
		$this->subject = array_merge( $this->subject, $_addition );

		return $this;
	}

	/**
	 * Conditionally append an array segment to the inner subject
	 *  - Note, $_addition is a computed value, if it's the output of a computation
	 *    it is always computed, $_should_append does not block the operation
	 *
	 * @param array $_addition      Array segment to append
	 * @param bool  $_should_append Whether to append $_addition to the array
	 *
	 * @return Arrays
	 */
	public function appendMaybe( array $_addition, bool $_should_append ): Arrays {
		if ( $_should_append ) {
			$this->subject = array_merge( $this->subject, $_addition );
		}

		return $this;
	}

	/**
	 * Chainable wrapper to run array_filter on the internal subject
	 *
	 * @param ?callable $_function Optional function to filter items (item) => bool
	 *
	 * @return Arrays
	 */
	public function filter( ?callable $_function ): Arrays {
		$this->subject = array_filter( $this->toArray(), $_function );

		return $this;
	}

	/**
	 * Current inner subject returned to the standard array type
	 *
	 * @return array
	 */
	public function toArray(): array {
		return $this->subject;
	}

	/**
	 * Chainable wrapper for array_unique to sort and remove duplicate arrays values
	 *    - Default of SORT_REGULAR to allow sorting/filtering of sub-arrays
	 *
	 * @param int $_sort_flags PHP array sort flags
	 *
	 * @return Arrays
	 */
	public function filterUnique( int $_sort_flags = SORT_REGULAR ): Arrays {
		$this->subject = array_unique( $this->subject, $_sort_flags );

		return $this;
	}

	/**
	 * Join array elements together with a separating string, assumes all elements are strings
	 *
	 * @param string $_separator Separator placed between array segments, default of comma
	 *
	 * @return string
	 */
	public function join( string $_separator = ',' ): string {
		return implode( $_separator, $this->subject );
	}
}
