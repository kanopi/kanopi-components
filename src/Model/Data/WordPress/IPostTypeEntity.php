<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use WP_Post;

/**
 * Indexed entity with common WordPress post type properties
 *
 * @package kanopi/components
 */
interface IPostTypeEntity extends IIndexedEntity {
	/**
	 * Create entity model form WordPress specific WP_Post object
	 *
	 * @param WP_Post $_post Source post entity
	 *
	 * @return IPostTypeEntity
	 */
	public static function fromWPPost( WP_Post $_post ): IPostTypeEntity;

	/**
	 * Effective post content
	 *
	 * @return string
	 */
	public function content(): string;

	/**
	 * Any extra fields needed for the post type as wp_insert_post array arguments
	 *
	 * @return array
	 * @see wp_insert_post
	 */
	public function extraInsertFieldMapping(): array;

	/**
	 * Mapping from meta_key => meta_value
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	public function metaFieldMapping(): array;

	/**
	 * Effective post status
	 *
	 * @return string
	 */
	public function status(): string;

	/**
	 * Mapping from taxonomy => term_list_or_array
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	public function taxonomyTermMapping(): array;

	/**
	 * Effective post title
	 *
	 * @return string
	 */
	public function title(): string;

	/**
	 * Update the entity content
	 *
	 * @param string $_content New content
	 *
	 * @return IPostTypeEntity
	 */
	public function updateContent( string $_content ): IPostTypeEntity;

	/**
	 * Update the entity status
	 *
	 * @param string $_status New status
	 *
	 * @return IPostTypeEntity
	 */
	public function updateStatus( string $_status ): IPostTypeEntity;

	/**
	 * Update the entity title
	 *
	 * @param string $_title New title
	 *
	 * @return IPostTypeEntity
	 */
	public function updateTitle( string $_title ): IPostTypeEntity;

	/**
	 * Change/remove and underlying WP_Term instance for the term entity
	 *
	 * @param ?WP_Post $_post Source post entity
	 *
	 * @return IPostTypeEntity
	 */
	public function updateWPPost( ?WP_Post $_post ): IPostTypeEntity;
}
