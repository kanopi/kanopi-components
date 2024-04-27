<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * Post type with featured image
 *
 * @package kanopi/components
 */
interface FeaturedImagePostEntity extends IPostTypeEntity {
	/**
	 * Read an associated featured image identifier
	 *
	 * @return int
	 */
	public function featuredImageIdentifier(): int;

	/**
	 * Read an incoming featured image
	 *
	 * @return MediaPostEntity
	 */
	public function readNewFeaturedImage(): MediaPostEntity;

	/**
	 * Change the new featured image
	 *
	 * @param MediaPostEntity $_image Feature image details
	 * @return void
	 */
	public function updateNewFeaturedImage( MediaPostEntity $_image ): void;

	/**
	 * Change the featured image identifier
	 *
	 * @param int $_attachmentId Attachment identifier
	 * @return void
	 */
	public function updateFeaturedImageIdentifier( int $_attachmentId ): void;
}
