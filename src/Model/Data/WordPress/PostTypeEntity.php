<?php
namespace Kanopi\Components\Model\Data\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Transformers\Arrays;

/**
 * Base indexed entity model for processing WordPress Post Types
 *	To use:
 * 		- Implement all abstract methods, all mapping methods can return an empty array([]) to be safely ignored
 *      - System post_id is set to 0, defaults for insert mode, it can be externally set with updateIndexIdentifier
 * 		- post_content, post_status, and post_title are all required and set to defaults
 */
abstract class PostTypeEntity implements IIndexedEntity {
	/**
	 * Any extra fields needed for the post type as wp_insert_post array arguments
	 *
	 * @return array
	 * @see wp_insert_post
	 */
	abstract function extraInsertFieldMapping(): array;

	/**
	 * System post content
	 *
	 * @var string
	 */
	public string $postContent = '';

	/**
	 * System post identifier
	 *
	 * @var int
	 */
	public int $postId = 0;

	/**
	 * System post status
	 *
	 * @var string
	 */
	public string $postStatus = 'publish';

	/**
	 * System post title
	 *
	 * @var string
	 */
	public string $postTitle = '';

	/**
	 * @inheritDoc
	 */
	abstract function entityName(): string;

	/**
	 * @inheritDoc
	 */
	function indexIdentifier(): int {
		return $this->postId;
	}

	/**
	 * Mapping from meta_key => meta_value
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	abstract function metaFieldMapping(): array;

	/**
	 * Mapping from taxonomy => term_list_or_array
	 *    - Only set keys will be written/overwritten
	 *
	 * @return array
	 */
	abstract function taxonomyTermMapping(): array;

	/**
	 * @inheritDoc
	 * @see wp_insert_post
	 */
	function systemTransform(): array {
		return Arrays::from( [
			'post_status'  => $this->postStatus,
			'post_type'    => $this->entityName(),
			'post_content' => $this->postContent,
			'post_title'   => $this->postTitle,
		] )
			->append_maybe( [ 'tax_input' => $this->taxonomyTermMapping() ], !empty( $this->taxonomyTermMapping() ) )
			->append_maybe( [ 'meta_input' => $this->metaFieldMapping() ], !empty( $this->metaFieldMapping() ) )
			->append_maybe( $this->extraInsertFieldMapping(), !empty( $this->extraInsertFieldMapping() ) )
			->unique()
			->toArray();
	}

	/**
	 * @inheritDoc
	 */
	abstract function uniqueIdentifier(): string;

	/**
	 * @inheritDoc
	 */
	function updateIndexIdentifier( int $_index ): void {
		$this->postId = $_index;
	}

	/**
	 * @inheritDoc
	 */
	abstract function version(): string;
}