<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use WP_Post;

interface IPostTypeEntity extends IIndexedEntity {
	/**
	 * Create entity model form WordPress specific WP_Post object
	 *
	 * @param WP_Post $_post
	 *
	 * @return IPostTypeEntity
	 */
	static function fromWPPost( WP_Post $_post ): IPostTypeEntity;

	/**
	 * Effective post content
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
	 * Effective post status
	 *
	 * @return string
	 */
	function status(): string;

	/**
	 * Mapping from taxonomy => term_list_or_array
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	function taxonomyTermMapping(): array;

	/**
	 * Effective post title
	 *
	 * @return string
	 */
	function title(): string;

	/**
	 * Update the entity content
	 *
	 * @param string $_content
	 *
	 * @return IPostTypeEntity
	 */
	function updateContent( string $_content ): IPostTypeEntity;

	/**
	 * Update the entity status
	 *
	 * @param string $_content
	 *
	 * @return IPostTypeEntity
	 */
	function updateStatus( string $_content ): IPostTypeEntity;

	/**
	 * Update the entity title
	 *
	 * @param string $_content
	 *
	 * @return IPostTypeEntity
	 */
	function updateTitle( string $_content ): IPostTypeEntity;

	/**
	 * Change/remove and underlying WP_Term instance for the term entity
	 *
	 * @param ?WP_Post $_post
	 *
	 * @return IPostTypeEntity
	 */
	function updateWPPost( ?WP_Post $_post ): IPostTypeEntity;
}
