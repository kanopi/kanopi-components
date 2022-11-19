<?php

namespace Kanopi\Components\Model\Data\WordPress;

trait PostTypeEntity {
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
	function indexIdentifier(): int {
		return $this->postId;
	}

	/**
	 * @inheritDoc
	 */
	function updateIndexIdentifier( int $_index ): void {
		$this->postId = $_index;
	}
}