<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\DomTransform;
use Kanopi\Components\Blocks\Model\FlowElementTransform;
use Kanopi\Components\Model\Data\WordPress\Attachment;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Services\System\IIndexedEntityWriter;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Transform an image found inline for another block (like paragraph) to a WordPress Media library entity
 *
 * @package kanopi/components
 */
class ImageInline implements DomTransform {
	use FlowElementTransform;

	/**
	 * Attachments Service to lookup WordPress image attachment URLs
	 *
	 * @var IIndexedEntityWriter
	 */
	protected IIndexedEntityWriter $imageService;

	/**
	 * Image transform constructor
	 *
	 * @param IIndexedEntityWriter $_attachmentService Attachment lookup service
	 */
	public function __construct( IIndexedEntityWriter $_attachmentService ) {
		$this->imageService = $_attachmentService;
	}

	/**
	 * DOMNode to WordPress Template with Class Style (spacing deliberate for block editor format checks)
	 */
	const TEMPLATE = '<img %1$s>';

	/**
	 * Transform a Node into an Inline Image
	 *  - Returns empty if there is no src URL (unlikely as that is invalid)
	 *
	 * @param HtmlPageCrawler $_node Node to transform
	 *
	 * @return string
	 */
	protected function processBlockTransformation( HtmlPageCrawler $_node ): string {
		$tagAttributes = Arrays::from( [] );

		// Read the source node attributes
		$nodeSrcUrl = urldecode( $_node->attr( 'src' ) ?? '' );

		// Check for a Site Attachment
		$nodeAttachment = $this->readImageSrcFile( $nodeSrcUrl );
		$useSiteImage   = ! empty( $nodeAttachment );

		// Process the URL, either a Site Attachment or External URL; tag attributes are explicit ordered
		if ( $useSiteImage ) {
			$tagAttributes->append( [ sprintf( 'class="wp-image-%s"', $nodeAttachment->indexIdentifier() ) ] );
			$tagAttributes->append( [ sprintf( 'src="%s"', $nodeAttachment->url() ) ] );
		} else {
			$tagAttributes->append( [ sprintf( 'src="%s"', $nodeSrcUrl ) ] );
		}

		$tagAttributes->append(
			[
				sprintf( 'alt="%s"', htmlspecialchars( $_node->attr( 'alt' ) ?? '' ) ),
			]
		);

		return ! empty( $nodeSrcUrl )
			? sprintf(
				self::TEMPLATE,
				$tagAttributes->join( ' ' )
			)
			: '';
	}

	/**
	 * Read any attachment associated with the current source URL, null if none found
	 *
	 * @param string $_sourceImageUrl Source image URL
	 *
	 * @return Attachment|null
	 */
	protected function readImageSrcFile( string $_sourceImageUrl ): ?Attachment {
		$srcFilePathParts = explode( '/', $_sourceImageUrl );
		$filePath         = is_array( $srcFilePathParts ) && 0 < count( $srcFilePathParts )
			? $srcFilePathParts[ count( $srcFilePathParts ) - 1 ]
			: '';

		try {
			$attachment = $this->imageService->readByUniqueIdentifier( $filePath );

			if ( null !== $attachment && ! is_a( $attachment, Attachment::class ) ) {
				$attachment = null;
			}
		} catch ( SetReaderException $_exception ) {
			$attachment = null;
		}

		return $attachment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'img';
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform( Crawler $_node, bool $_allowEmpty ): string {
		$node = HtmlPageCrawler::create( $_node );

		return $this->matchesTransform( $_node )
			? $this->processBlockTransformation( $node )
			: '';
	}
}
