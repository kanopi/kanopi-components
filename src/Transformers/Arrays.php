<?php

namespace Kanopi\Components\Transformers;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Common array manipulation in a state machine with a fluent interface
 *
 * @package kanopi/components
 */
class Arrays implements IteratorAggregate, Countable {
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
	 * Build a fresh/new, empty Arrays structure
	 *
	 * @return Arrays
	 */
	public static function fresh(): Arrays {
		return new Arrays( [] );
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
	 * Add a single value to the end of the inner subject
	 *  - Not type-safe, does not validate type against the rest of the array, mixed types can occur
	 *
	 * @param mixed $_addition Added value
	 *
	 * @return Arrays
	 */
	public function add( mixed $_addition ): Arrays {
		$this->subject[] = $_addition;

		return $this;
	}

	/**
	 * Conditionally append an array to the inner subject
	 *
	 * @param mixed         $_addition      Array segment to append
	 * @param callable|bool $_should_append Function returns a bool of whether to append $_addition to the array
	 *
	 * @return Arrays
	 */
	public function addMaybe( mixed $_addition, callable|bool $_should_append ): Arrays {
		$isAppended = is_callable( $_should_append ) ? call_user_func( $_should_append ) : $_should_append;
		if ( $isAppended ) {
			$this->subject[] = $_addition;
		}

		return $this;
	}

	/**
	 * Whether any internal subject value matches a test function
	 *  - $_test function is passed each item in the internal subject and stops testing once a match is found
	 *  - $_test function must return a boolean value of true to match, otherwise, considered false
	 *
	 * @param callable $_test Callable test function of this form - function( mixed $item ): bool
	 * @return bool
	 */
	public function any( callable $_test ): bool {
		$match = false;

		foreach ( $this->subject as $item ) {
			$match = is_callable( $_test ) ? call_user_func( $_test, $item ) : false;

			if ( true === $match ) {
				break;
			}
		}

		return $match;
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
	 * Whether the subject collection contains a given key (isset)
	 *
	 * @param mixed $_key Key to verify exists in the subject collection
	 *
	 * @return bool
	 */
	public function containsKey( mixed $_key ): bool {
		return isset( $this->subject[ $_key ] );
	}

	/**
	 * Whether the subject collection contains a given value (in_array)
	 *
	 * @param mixed $_value Value to search in the subject collection
	 *
	 * @return bool
	 */
	public function containsValue( mixed $_value ): bool {
		return in_array( $_value, $this->subject, true );
	}

	/**
	 * {@inheritDoc}
	 */
	public function count(): int {
		return ! empty( $this->subject ) ? count( $this->subject ) ?? 0 : 0;
	}

	/**
	 * Empty the current internal subject
	 *
	 * @return Arrays
	 */
	public function empty(): Arrays {
		$this->subject = [];

		return $this;
	}

	/**
	 * Ensure an Arrays instance exists at the provided index and returns a reference to the sub array for chaining
	 *  - WARNING: Destructive - If the index is not an array or Arrays, the index is replaced with an Arrays instance
	 *
	 * @param int|string $_index Index to ensure
	 *
	 * @return Arrays
	 */
	public function ensureSubArray( int|string $_index ): Arrays {
		$isSet    = isset( $this->subject[ $_index ] );
		$isArray  = $isSet && is_array( $this->subject[ $_index ] );
		$isArrays = $isSet && is_a( $this->subject[ $_index ], self::class );

		if ( ! ( $isArray || $isArrays ) ) {
			$this->subject[ $_index ] = self::fresh();
		}

		if ( $isArray ) {
			$this->subject[ $_index ] = self::from( $this->subject[ $_index ] );
		}

		return $this->subject[ $_index ];
	}

	/**
	 * Chainable wrapper to run array_filter on the internal subject
	 *
	 * @param ?callable $_function Optional function to filter items (item, key) => bool
	 *
	 * @return Arrays
	 */
	public function filter( ?callable $_function ): Arrays {
		$this->subject = array_filter( $this->toArray(), $_function, ARRAY_FILTER_USE_BOTH );

		return $this;
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
	 * Interface compliance to allow iteration in compliant structures like foreach loops
	 *
	 * @return Traversable
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->subject );
	}

	/**
	 * Whether the internal subject is empty
	 *
	 * @return bool
	 */
	public function isEmpty(): bool {
		return empty( $this->subject );
	}

	/**
	 * Read the set of array keys for the top-level internal subject
	 *
	 * @return array
	 */
	public function keys(): array {
		return $this->isEmpty() ? [] : array_keys( $this->subject );
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

	/**
	 * Chainable wrapper to run array_map on the internal subject
	 *
	 * @param callable $_function Map internal collection to a new internal collection
	 *
	 * @return Arrays
	 */
	public function map( callable $_function ): Arrays {
		$this->subject = array_map( $_function, $this->subject );

		return $this;
	}

	/**
	 * Read the value stored at a given index, null if it does not exist
	 *
	 * @param int|string $_index Index to read
	 *
	 * @return mixed
	 */
	public function readIndex( int|string $_index ): mixed {
		return $this->subject[ $_index ] ?? null;
	}

	/**
	 * Read all sub-Arrays beneath a given index (nothing for all) into a standard array structure
	 *
	 * @param int|string|null $_index (Optional) starting index
	 *
	 * @return array
	 */
	public function readSubArrays( int|string|null $_index = null ): array {
		// Isolate the index or root subject
		$start = null !== $_index ? $this->readIndex( $_index ) : $this->subject;

		// Unwrap any direct Arrays entity
		$subject = is_a( $start, self::class ) ? $start->readSubArrays() : $start;

		// Iterate through all internal indices
		$nested = [];
		foreach ( $subject ?? [] as $key => $value ) {
			$nested[ $key ] = is_a( $value, self::class ) ? $value->readSubArrays() : $value;
		}

		return $nested;
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
	 * Write (add/update) a single value at a given index
	 *  - Not type-safe, does not validate type against the rest of the array, mixed types can occur
	 *  - Overwrites any existing value at the index
	 *
	 * @param int|string $_index    Index to write value
	 * @param mixed      $_addition Written value
	 *
	 * @return Arrays
	 */
	public function writeIndex( int|string $_index, mixed $_addition ): Arrays {
		$this->subject[ $_index ] = $_addition;

		return $this;
	}
}
