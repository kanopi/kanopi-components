<?php

namespace Kanopi\Utilities\Model\Collection;

use ArrayIterator;

class EntityIterator extends ArrayIterator {
	/**
	 * Expected entity type
	 *
	 * @var string
	 */
	protected string $valid_entity_type;

	/**
	 * Construct an iterator which validates all internal entities are of a given type
	 *
	 * @param array  $_entities
	 * @param string $_valid_entity_type
	 */
	public function __construct( array $_entities, string $_valid_entity_type ) {
		parent::__construct( $_entities );

		$this->valid_entity_type = $_valid_entity_type;
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
		return is_a( $this->current(), $this->valid_entity_type );
	}

	/**
	 * @return bool
	 */
	protected function checkValidScalarType(): bool {
		$type = gettype( $this->current() );
		return 'object' !== $type && $this->valid_entity_type === $type;
	}

	/**
	 * @inheritDoc
	 */
	public function valid(): bool {
		return parent::valid() && $this->checkEntity();
	}
}