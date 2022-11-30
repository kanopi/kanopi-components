<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;

interface IPostTypeEntity extends IIndexedEntity  {
	/**
	 * System post content
	 *
	 * @return string
	 */
	function content(): string;

	/**
	 * Any extra fields needed for the post type as wp_insert_post array arguments
	 *
	 * @return array
	 * @see wp_insert_post
	 */
	function extraInsertFieldMapping(): array;

	/**
	 * Mapping from meta_key => meta_value
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	function metaFieldMapping(): array;

	/**
	 * System post status
	 *
	 * @return string
	 */
	function status(): string;

	/**
	 * System post title
	 *
	 * @return string
	 */
	function title(): string;

	/**
	 * Mapping from taxonomy => term_list_or_array
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	function taxonomyTermMapping(): array;
}