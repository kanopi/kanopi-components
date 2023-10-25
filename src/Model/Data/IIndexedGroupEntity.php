<?php

namespace Kanopi\Components\Model\Data;

/**
 * Entity used in collections segmented by Groups of a given key/name
 *
 * @package kanopi/components
 */
interface IIndexedGroupEntity extends IIndexedEntity {
	/**
	 * Key shared between grouped entities
	 *
	 * @return string
	 */
	public function groupKey(): string;
}
