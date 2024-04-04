<?php

namespace Kanopi\Components\Processor\WordPress;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\WordPress\FeaturedImagePostEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Processor\Recurrent\BatchedCursorUpdate;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Services\System\WordPress\ImageWriter;
use WP_Error;

/**
 * Manage featured images on supported post entities
 *
 * @package kanopi/components
 */
abstract class FeaturedImageProcessor extends BatchedCursorUpdate {
	/**
	 * Media Library Image Import Service
	 *
	 * @return ImageWriter
	 */
	abstract protected function imageService(): ImageWriter;

	/**
	 * Media Library to Post Attachment Service
	 *
	 * @return IIndexedEntityWriter
	 */
	abstract protected function attachmentService(): IIndexedEntityWriter;

	/**
	 * Process any newly requested featured image, maintains current on error
	 *
	 * @param FeaturedImagePostEntity $_entity     External entity to process
	 * @param int                     $_existingId Any existing associated featured image, fallback on error
	 * @return void
	 */
	protected function processFeaturedImage( FeaturedImagePostEntity $_entity, int $_existingId = 0 ): void {
		$image = $_entity->readNewFeaturedImage();
		if ( ! $image->useFeaturedImage() ) {
			return;
		}

		// WordPress will scale large images, to accommodate this, use a regular expression to check
		try {
			$filename        = pathinfo( $image->fileName, PATHINFO_FILENAME );
			$extension       = pathinfo( $image->fileName, PATHINFO_EXTENSION );
			$existingImage   = $this->attachmentService()->readByUniqueIdentifier( "{$filename}(-scaled)?\.{$extension}" );
			$existingImageId = $existingImage?->indexIdentifier() ?? 0;
			$nextImageId     = 0 < $existingImageId ? $existingImageId : $this->imageService()->import( $image );
			$_entity->updateFeaturedImageIdentifier( $nextImageId );
		} catch ( SetReaderException ) {
			$_entity->updateFeaturedImageIdentifier( $_existingId );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processNewSystemEntity( IIndexedEntity $_incoming ): IIndexedEntity {
		if ( false === $this->isDryRunEnabled() ) {
			if ( $_incoming instanceof FeaturedImagePostEntity ) {
				$this->processFeaturedImage( $_incoming );
			}
			$_incoming = $this->systemService()->create( $_incoming );
		}

		$this->processStatistics()->created( $_incoming->indexIdentifier() );
		return $_incoming;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processExistingSystemEntity(
		IIndexedEntity $_incoming,
		IIndexedEntity $_existing
	): IIndexedEntity {
		$_incoming->updateIndexIdentifier( $_existing->indexIdentifier() );

		if ( $this->shouldEntityUpdate( $_existing, $_incoming ) ) {
			if ( false === $this->isDryRunEnabled() ) {
				if ( $_incoming instanceof FeaturedImagePostEntity && $_existing instanceof FeaturedImagePostEntity ) {
					$this->processFeaturedImage( $_incoming, $_existing->featuredImageIdentifier() );
				}
				$this->systemService()->update( $_incoming );
			}

			$this->processStatistics()->updated( $_incoming->indexIdentifier() );
		} else {
			$this->processStatistics()->skipped( $_incoming->indexIdentifier() );
		}

		return $_incoming;
	}
}
