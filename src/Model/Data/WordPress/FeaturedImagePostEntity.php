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
	 * @return FeaturedImage
	 */
	public function readNewFeaturedImage(): FeaturedImage;

	/**
	 * Change the new featured image
	 *
	 * @param FeaturedImage $_image Feature image details
	 * @return void
	 */
	public function updateNewFeaturedImage( FeaturedImage $_image ): void;

	/**
	 * Change the featured image identifier
	 *
	 * @param int $_attachmentId Attachment identifier
	 * @return void
	 */
	public function updateFeaturedImageIdentifier( int $_attachmentId ): void;
}
