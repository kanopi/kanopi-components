<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\StandardBlock;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Transform an ol tag into a WordPress List Block of Ordered Type with Inner List Item blocks
 *
 * @package kanopi/components
 */
class OrderedList implements RecursiveDomTransform {
	use StandardBlock;
	use ParentTransform;

	/**
	 * {@inheritDoc}
	 */
	protected function blockName(): string {
		return 'wp:list';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function blockTagName(): string {
		return 'ol';
	}

	/**
	 * {@inheritDoc}
	 *
	 *  - Overridden to set a specific order on Block attributes to mitigate editor block validation errors
	 */
	protected function filterNodeAndAttributes(
		Crawler $_node,
		Arrays $_blockAttributes,
		Arrays $_domAttributes
	): void {
		// Proxy the existing block attributes into placeholder attributes array
		$blockAttributes = Arrays::from( [ '"ordered":true' ] )->append( $_blockAttributes->toArray() );

		// Process the Ordered List start attribute, add it if greater than 1 (default)
		$nodeStart = intval( $_node->attr( 'start' ) ?? 0 );
		$blockAttributes->appendMaybe(
			[ sprintf( '"start":%d', $nodeStart ) ],
			1 < $nodeStart
		);
		$_domAttributes->appendMaybe(
			[ sprintf( 'start="%d"', $nodeStart ) ],
			1 < $nodeStart
		);

		// Clear the original block attributes and add the revised set of attributes
		$_blockAttributes->empty()->append( $blockAttributes->toArray() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'ol';
	}
}
