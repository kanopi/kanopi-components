<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\DomTransform;
use Kanopi\Components\Blocks\Model\FlowElementTransform;
use Kanopi\Components\Model\Data\Stream\StreamCursorPagination;
use Kanopi\Components\Model\Data\WordPress\MediaPostEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\External\ExternalCursorStreamReader;
use Kanopi\Components\Services\System\WordPress\MediaFileWriter;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Transform an img tag into a WordPress Image Block
 *
 * @package kanopi/components
 */
class WordPressImage implements DomTransform {
	use FlowElementTransform;

	/**
	 * Image transform constructor
	 *
	 * @param ExternalCursorStreamReader $mediaReader    Client to read external REST API for media
	 * @param MediaFileWriter            $mediaService   Attachment lookup service
	 * @param IEntitySet                 $mediaTransform Transform external source into set of MediaPostEntity objects
	 * @param array                      $importDomains  Set of domains to import images even if there is no Media Library API match
	 */
	public function __construct(
		protected ExternalCursorStreamReader $mediaReader,
		protected MediaFileWriter $mediaService,
		protected IEntitySet $mediaTransform,
		protected array $importDomains = []
	) {}

	/**
	 * DOMNode to WordPress Template with Class Style (spacing deliberate for block editor format checks)
	 */
	const TEMPLATE = '<!-- wp:image %1$s --><figure class="wp-block-image size-full"><img %2$s /></figure><!-- /wp:image -->';

	/**
	 * {@inheritDoc}
	 */
	public function matchesTransform( Crawler $_node ): bool {
		return $this->checkTagName( $_node->nodeName() )
			&& (
				0 < $this->readMediaId( $_node )
				|| ! empty( $this->readMediaUrl( $_node ) )
			);
	}

	/**
	 * Read the media ID from a WordPress rendered media image
	 *  - 0 if unmatched
	 *
	 * @param Crawler $_element Element to check
	 *
	 * @return int
	 */
	public function readMediaId( Crawler $_element ): int {
		$attribute    = $_element->matches( '*' ) ? $_element->attr( 'class' ) : null;
		$imageIdCount = preg_match( '/wp\-image\-(\d+)/i', $attribute ?? '', $classId );

		return 0 < $imageIdCount ? $classId[1] : 0;
	}

	/**
	 * Checks for any matching media URLs from import domains
	 *  - 0 if unmatched
	 *
	 * @param Crawler $_element Element to check
	 *
	 * @return int
	 */
	public function readMediaUrl( Crawler $_element ): string {
		$url        = $_element->matches( '*' ) ? $_element->attr( 'src' ) : '';
		$matchedUrl = '';

		if ( ! empty( $this->importDomains ) && ! empty( $url ) ) {
			foreach ( $this->importDomains as $domain ) {
				$matchCount = preg_match( '/' . preg_quote( $domain, '/' ) . '/i', $url, $classId );

				if ( 0 < $matchCount ) {
					$matchedUrl = $url;
					break;
				}
			}
		}

		return $matchedUrl;
	}

	/**
	 * Transform a Node into an Image Block
	 *  - Returns empty if there is no src URL (unlikely as that is invalid)
	 *
	 * @param Crawler              $_node Node to transform
	 * @param MediaPostEntity|null $_attachment Media attachment to use
	 *
	 * @return string
	 */
	protected function processBlockTransformation( Crawler $_node, ?MediaPostEntity $_attachment ): string {
		$blockAttributes = Arrays::from( [ '"sizeSlug":"full"' ] );
		$tagAttributes   = Arrays::from( [] );

		// Read the source node attributes
		$nodeSrcUrl = urldecode( $_node->attr( 'src' ) ?? '' );

		// Process the URL, either a Site Attachment or External URL; tag attributes are explicit ordered
		if ( null !== $_attachment ) {
			$nodeSrcUrl = $this->mediaService->readSystemUrl( $_attachment );
			$blockAttributes->append( [ sprintf( '"id":%d', $_attachment->indexIdentifier() ) ] );
			$blockAttributes->append( [ '"linkDestination":"none"' ] );
			$tagAttributes->append( [ sprintf( 'src="%s"', $nodeSrcUrl ) ] );
			$tagAttributes->append( [ sprintf( 'class="wp-image-%s"', $_attachment->indexIdentifier() ) ] );
		}
		else {
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
				sprintf( '{%s}', $blockAttributes->join( ',' ) ),
				$tagAttributes->join( ' ' )
			)
			: '';
	}

	/**
	 * Read any attachment associated with the current source ID (preferred) or URL (fallback), null if none found
	 *
	 * @param int    $_imageId  Source image ID
	 * @param string $_imageUrl Source image URL
	 *
	 * @return MediaPostEntity|null
	 */
	protected function readMediaAttachment( int $_imageId, string $_imageUrl ): ?MediaPostEntity {
		$mediaAttachment = null;

		try {
			$this->mediaReader->readStream( $_imageId, new StreamCursorPagination( 1 ), $this->mediaTransform );
			$mediaItems  = $this->mediaReader->read();
			$importMedia = $mediaItems->valid() && $mediaItems->current() instanceof MediaPostEntity ? $mediaItems->current() : null;

			if ( null !== $importMedia ) {
				$mediaAttachment = $this->mediaService->readByUniqueIdentifier( $importMedia->externalUrl() );
				if ( null === $mediaAttachment ) {
					$mediaAttachment = $importMedia;
					$importedId      = $this->mediaService->importFile( $importMedia );
					$mediaAttachment->updateIndexIdentifier( $importedId );
				}
			}
			elseif ( ! empty( $_imageUrl ) ) {
				$mediaAttachment = $this->mediaService->readByUniqueIdentifier( $_imageUrl );
			}
		}
		catch ( SetReaderException $_exception ) {
			$mediaAttachment = null;
		}

		return $mediaAttachment;
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
		$mediaId         = $this->readMediaId( $_node );
		$mediaUrl        = 1 > $mediaId ? $this->readMediaUrl( $_node ) : '';
		$shouldTransform = 0 < $mediaId || ! empty( $mediaUrl );
		$mediaAttachment = $this->readMediaAttachment( $mediaId, $mediaUrl );
		return $shouldTransform ? $this->processBlockTransformation( $_node, $mediaAttachment ) : '';
	}
}
