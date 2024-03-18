<?php

namespace Kanopi\Components\Processor\WordPress;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\FlowElementTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Process incoming content into Blocks based on registered Contexts and Transforms
 *  - Implements RecursiveDomTransform, BlockSiteContent, and SingleContext interfaces
 *
 * @package kanopi/components
 */
trait StandardBlockContent {
	use FlowElementTransform;
	use ParentTransform;
	use SingleCurrentContext;

	/**
	 * Clear the current context and any other internal state variables
	 */
	protected function clearState(): void {
		$this->clearCurrentContext();
	}

	/**
	 * Pre-process the content to normalize spaces and decode HTML-entities before wrapping to a simulated HTML body
	 *
	 * @param string $_content Incoming content
	 *
	 * @return string
	 */
	private function preProcessContent( string $_content ): string {
		$decodedContent = trim(
			preg_replace(
				'/[\s\t\n\r]+/mu',
				' ',
				html_entity_decode( trim( $_content ), ENT_HTML5 | ENT_QUOTES, 'UTF-8' )
			)
		);

		return "<html lang='en'><body>${decodedContent}</body></html>";
	}

	/**
	 * @param string $_content Incoming DOM as a string
	 *
	 * @return string
	 */
	public function process( string $_content ): string {
		$page = new Crawler( $this->preProcessContent( $_content ) );
		$body = $page->filterXPath( '//body' );

		$this->preTransformEvents( $body );

		return $this->transform( $body, false );
	}


	/**
	 * Events performed on the Processor and Crawler created for the content prior to running transforms
	 *
	 * @param Crawler $_body Incoming DOM body element
	 */
	public function preTransformEvents( Crawler $_body ): void {
		$this->clearState();
		$this->removeEmptyChildrenByTagName( $_body );
	}

	/**
	 * @see RecursiveDomTransform::supportedTagName
	 */
	public function supportedTagName(): string {
		return 'body';
	}

	/**
	 * @see RecursiveDomTransform::transform
	 *
	 * @param Crawler $_node       Current node to process
	 * @param bool    $_allowEmpty Whether to allow nodes which are empty following transformation
	 */
	public function transform( Crawler $_node, bool $_allowEmpty ): string {
		return $this->transformContextCollection( $_node, $_node->children( '*' ), $_allowEmpty );
	}
}
