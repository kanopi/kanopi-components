<?php

namespace Kanopi\Utilities\Model\Data;

interface IIndexedGroupEntity extends IIndexedEntity {
	/**
	 * Key shared between grouped entities
	 *
	 * @return string
	 */
	function groupKey(): string;
}