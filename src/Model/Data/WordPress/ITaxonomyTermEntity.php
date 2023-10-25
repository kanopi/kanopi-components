<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use WP_Term;

/**
 * Indexed entity with common WordPress taxonomy term properties
 *
 * @package kanopi/components
 */
interface ITaxonomyTermEntity extends IIndexedEntity {
	/**
	 * Create entity model form WordPress specific WP_Term object
	 *
	 * @param WP_Term $_term Original term entity
	 *
	 * @return ITaxonomyTermEntity
	 */
	public static function fromWPTerm( WP_Term $_term ): ITaxonomyTermEntity;

	/**
	 * System term description
	 *
	 * @return string
	 */
	public function description(): string;

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

	/**
	 * Change the terms description
	 *
	 * @param string $_description New term description
	 *
	 * @return ITaxonomyTermEntity
	 */
	public function updateDescription( string $_description ): ITaxonomyTermEntity;

	/**
	 * Change the terms name
	 *
	 * @param string $_name New term name
	 *
	 * @return ITaxonomyTermEntity
	 */
	public function updateName( string $_name ): ITaxonomyTermEntity;

	/**
	 * Change the terms parent identifier
	 *
	 * @param int $_parentId New parent index indentifier
	 *
	 * @return ITaxonomyTermEntity
	 */
	public function updateParentId( int $_parentId ): ITaxonomyTermEntity;

	/**
	 * Change the terms slug
	 *
	 * @param string $_slug New term slug
	 *
	 * @return ITaxonomyTermEntity
	 */
	public function updateSlug( string $_slug ): ITaxonomyTermEntity;

	/**
	 * Change/remove and underlying WP_Term instance for the term entity
	 *
	 * @param ?WP_Term $_term Original term entity
	 *
	 * @return ITaxonomyTermEntity
	 */
	public function updateWPTerm( ?WP_Term $_term ): ITaxonomyTermEntity;
}
