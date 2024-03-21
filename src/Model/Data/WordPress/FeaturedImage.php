<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * Featured Image Property
 *
 * @package kanopi/components
 */
class FeaturedImage {
	/**
	 * FeaturedImage constructor
	 *
	 * @param string $externalUrl The external image URL
	 * @param string $fileName    The expected filename
	 * @param string $title       The image title
	 */
	public function __construct(
		public string $externalUrl = '',
		public string $fileName = '',
		public string $title = ''
	) {
	}

	/**
	 * Whether to use a featured image
	 *
	 * @return bool
	 */
	public function useFeaturedImage(): bool {
		return ! empty( $this->fileName ) && ! empty( $this->externalUrl );
	}
}
