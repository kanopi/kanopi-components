<?php

namespace Kanopi\Components\Model\Data\WordPress\Acf;

/**
 * Mapping for ACF Flex Content Areas associated with WordPress Posts
 *
 * @package kanopi-components
 */
class FlexContentMetaColumns {
	/**
	 * WordPress post system identifier
	 *
	 * @var int
	 */
	public int $postId;

	/**
	 * WordPress post status
	 *
	 * @var string
	 */
	public string $postStatus;

	/**
	 * WordPress post title
	 *
	 * @var string
	 */
	public string $postTitle;

	/**
	 * WordPress post type
	 *
	 * @var string
	 */
	public string $postType;

	/**
	 * Meta value containing the set of ACF Flex Content column types for the post
	 *
	 * @var array
	 */
	public array $flexContentHeaders;

	/**
	 * Meta key which maps to the ACF Flex content name/key
	 *
	 * @var string
	 */
	public string $flexContentType;
}
