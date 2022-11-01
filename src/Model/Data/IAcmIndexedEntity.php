<?php

namespace Kanopi\Utilities\Model\Data;

interface IAcmIndexedEntity extends IIndexedEntity {
	/**
	 * Core entity data (i.e. standard post type data)
	 *
	 * @return array
	 */
	function coreData(): iterable;

	/**
	 * Entity meta data
	 *
	 * @return array
	 */
	function metaData(): iterable;
}