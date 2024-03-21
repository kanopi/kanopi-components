<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\FeaturedImage;
use WP_Error;

/**
 * Image writing service uses the WP HTTP API and temp file system
 *
 * @package kanopi/components
 */
class StandardImageWriter implements ImageWriter {
	/**
	 * {@inheritDoc}
	 */
	public function import( FeaturedImage $_image ): int {
		// Download the image from the URL to the temp directory, must unlink the file later
		$downloadedFilePath = download_url( $_image->externalUrl );
		$attachmentId       = 0;

		if ( ! is_wp_error( $downloadedFilePath ) ) {
			// Move the downloaded file to a temporary location with the correct filename
			$destinationFilePath = dirname( $downloadedFilePath ) . '/' . $_image->fileName;

			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_rename -- download_url downloads to the temp directory
			rename( $downloadedFilePath, $destinationFilePath );

			// Add the image in the temp directory to the WordPress media library
			$imageId = media_handle_sideload(
				[
					'name'     => basename( $destinationFilePath ),
					'tmp_name' => $destinationFilePath,
				],
				0,
				$_image->title,
			);

			$attachmentId = is_a( $imageId, WP_Error::class ) ? 0 : $imageId;

			// Check if temp file removal is needed
			if ( file_exists( $destinationFilePath ) ) {
				// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- Removing temp directory
				unlink( $destinationFilePath );
			}
		}

		return $attachmentId;
	}
}
