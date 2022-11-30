<?php

namespace Kanopi\Components\Model\Data\WordPress;

trait PostTypeEntity {
	/**
	 * System post content
	 *
	 * @var string
	 */
	public string $_content = '';

	/**
	 * System post identifier
	 *
	 * @var int
	 */
	public int $_postId = 0;

	/**
	 * System post status
	 *
	 * @var string
	 */
	public string $_status = 'publish';

	/**
	 * System post title
	 *
	 * @var string
	 */
	public string $_title = '';

	/**
	 * @inheritDoc
	 */
	function content(): string {
		return $this->_content;
	}

	/**
	 * @inheritDoc
	 */
	function indexIdentifier(): int {
		return $this->_postId;
	}

	/**
	 * @inheritDoc
	 */
	function status(): string {
		return $this->_status;
	}

	/**
	 * @inheritDoc
	 */
	function title(): string {
		return $this->_title;
	}

	/**
	 * @inheritDoc
	 */
	function updateIndexIdentifier( int $_index ): void {
		$this->_postId = $_index;
	}
}