<?php

namespace Kanopi\Components\Transformers;

class Arrays {
	/**
	 * @var array
	 */
	protected array $subject;

	/**
	 * Arrays constructor, wraps the subject for chainable operations
	 *
	 * @param array $_subject
	 */
	function __construct( array $_subject ) {
		$this->subject = $_subject;
	}

	/**
	 * Factory to build a strings converter
	 *
	 * @param array $_subject
	 *
	 * @return Arrays
	 */
	static function from( array $_subject ): Arrays {
		return new Arrays( $_subject );
	}

	/**
	 * Append an array segment to this array
	 *
	 * @param array $_addition
	 *
	 * @return Arrays
	 */
	function append( array $_addition ): Arrays {
		$this->subject = array_merge( $this->subject, $_addition );

		return $this;
	}


	/**
	 * Maybe append an array segment to this array is $_should_append is true
	 *
	 * @param array $_addition
	 * @param bool $_should_append
	 *
	 * @return Arrays
	 */
	function append_maybe( array $_addition, bool $_should_append ): Arrays {
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
	function filter( ?callable $_function ): Arrays {
		$this->subject = array_filter( $this->toArray(), $_function );

		return $this;
	}

	/**
	 * @return array
	 */
	function toArray(): array {
		return $this->subject;
	}

	/**
	 * Chainable wrapper for array_unique to sort and remove duplicate arrays values
	 * 	- Sort regular to allow sorting/filtering of sub-arrays
	 *
	 * @param int   $_sort_flags
	 *
	 * @return Arrays
	 */
	function unique( int $_sort_flags = SORT_REGULAR ): Arrays {
		$this->subject = array_unique( $this->subject, $_sort_flags );

		return $this;
	}
}