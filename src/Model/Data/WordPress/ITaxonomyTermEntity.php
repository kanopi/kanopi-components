<?php

namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use WP_Term;

interface ITaxonomyTermEntity extends IIndexedEntity {
	/**
	 * System term description
	 *
	 * @return string
	 */
	function description(): string;

	/**
	 * Create entity model form WordPress specific WP_Term object
	 *
	 * @param WP_Term $_term
	 *
	 * @return ITaxonomyTermEntity
	 */
	static function fromWPTerm( WP_Term $_term ): ITaxonomyTermEntity;

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
	 * @param string $_description
	 *
	 * @return ITaxonomyTermEntity
	 */
	function updateDescription( string $_description ): ITaxonomyTermEntity;

	/**
	 * Change the terms name
	 *
	 * @param string $_name
	 *
	 * @return ITaxonomyTermEntity
	 */
	function updateName( string $_name ): ITaxonomyTermEntity;

	/**
	 * Change the terms parent identifier
	 *
	 * @param int $_parentId
	 *
	 * @return ITaxonomyTermEntity
	 */
	function updateParentId( int $_parentId ): ITaxonomyTermEntity;

	/**
	 * Change the terms slug
	 *
	 * @param string $_slug
	 *
	 * @return ITaxonomyTermEntity
	 */
	function updateSlug( string $_slug ): ITaxonomyTermEntity;

	/**
	 * Change/remove and underlying WP_Term instance for the term entity
	 *
	 * @param ?WP_Term $_term
	 *
	 * @return ITaxonomyTermEntity
	 */
	function updateWPTerm( ?WP_Term $_term ): ITaxonomyTermEntity;
}