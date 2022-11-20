<?php

namespace Kanopi\Components\Model\Data\WordPress;

class EntityTaxonomyFilter {
	/**
	 * Entity identifier to match for taxonomy references
	 *
	 * @var int
	 */
	public int $entity_id = 0;

	/**
	 * Set of taxonomy slugs to search find all term references against the entity
	 *
	 * @var array
	 */
	public array $taxonomies = [];

	/**
	 * Required fields/values validity check
	 *
	 * @return bool
	 */
	public function isValid(): bool {
		return !empty( $this->taxonomies ) && 0 < $this->entity_id;
	}
}