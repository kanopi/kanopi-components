<?php

namespace Kanopi\Components\Model\Collection;

use ArrayIterator;

class EntityIterator extends ArrayIterator {
	/**
	 * Expected entity type
	 *
	 * @var string
	 */
	protected string $validEntityType;

	/**
	 * Construct an iterator which validates all internal entities are of a given type
	 *
	 * @param array  $_entities
	 * @param string $_valid_entity_type
	 */
	public function __construct( array $_entities, string $_valid_entity_type ) {
		parent::__construct( $_entities );

		$this->validEntityType = $_valid_entity_type;
	}

	/**
	 * @return bool
	 */
	protected function checkEntity(): bool {
		return $this->checkValidScalarType() || $this->checkValidObjectType();
	}

	/**
	 * @return bool
	 */
	protected function checkValidObjectType(): bool {
		return is_a( $this->current(), $this->validEntityType );
	}

	/**
	 * @return bool
	 */
	protected function checkValidScalarType(): bool {
		$type = gettype( $this->current() );
		return 'object' !== $type && $this->validEntityType === $type;
	}

	/**
	 * @inheritDoc
	 */
	public function valid(): bool {
		return parent::valid() && $this->checkEntity();
	}

	/**
	 * Static generator to get an iterator
	 *
	 * @param array  $_entities
	 * @param string $_valid_entity_type
	 *
	 * @return EntityIterator
	 */
	public static function fromArray( array $_entities, string $_valid_entity_type ): EntityIterator {
		return new EntityIterator( $_entities, $_valid_entity_type );
	}
}