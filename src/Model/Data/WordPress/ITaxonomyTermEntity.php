<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;

interface ITaxonomyTermEntity extends IIndexedEntity {
	/**
	 * System term description
	 *
	 * @return string
	 */
	public function description(): string;

	/**
	 * System term identifier
	 *
	 * @return int
	 */
	function id(): int;

	/**
	 * Term name
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * System term parent identifier
	 *
	 * @return int
	 */
	public function parentId(): int;

	/**
	 * System term slug
	 *
	 * @return string
	 */
	public function slug(): string;
}