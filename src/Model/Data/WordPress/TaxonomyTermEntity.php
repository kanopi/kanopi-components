<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;

trait TaxonomyTermEntity {
	/**
	 * System term identifier
	 *
	 * @var int
	 */
	public int $_termId = 0;

	/**
	 * System term description
	 *
	 * @var string
	 */
	public string $_description = '';

	/**
	 * System term name
	 *
	 * @var string
	 */
	public string $_name = '';

	/**
	 * System term parent identifier
	 *
	 * @var int
	 */
	public int $_parentId = 0;

	/**
	 * System term slug
	 *
	 * @var string
	 */
	public string $_slug = '';

	/**
	 * @inheritDoc
	 */
	public function description(): string {
		return $this->_description;
	}

	/**
	 * @inheritDoc
	 */
	public function name(): string {
		return $this->_name;
	}

	/**
	 * @inheritDoc
	 */
	public function parentId(): int {
		return $this->_parentId;
	}

	/**
	 * @inheritDoc
	 */
	public function slug(): string {
		return $this->_slug;
	}

	/**
	 * @see IIndexedEntity::indexIdentifier()
	 */
	function indexIdentifier(): int {
		return $this->_termId;
	}

	/**
	 * @see IIndexedEntity::updateIndexIdentifier()
	 */
	function updateIndexIdentifier( int $_index ): void {
		$this->_termId = $_index;
	}
}