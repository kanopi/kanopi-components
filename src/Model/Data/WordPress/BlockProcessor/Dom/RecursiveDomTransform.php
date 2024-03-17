<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Manage recursive/nested sets of content transformations, for inner DOM elements
 *
 * @package kanopi/components
 */
interface RecursiveDomTransform extends DomTransform {
	/**
	 * Register an additional inner transformation
	 *
	 * @param DomTransform $_transform Transform to register
	 * @return void
	 */
	public function registerInnerTransform( DomTransform $_transform ): void;

	/**
	 * Run the node through all registered inner transforms
	 *
	 * @param Crawler $_node       Node to process
	 * @param bool    $_allowEmpty Whether empty inner tags are allowed
	 * @return string|null
	 */
	public function processInnerTransforms( Crawler $_node, bool $_allowEmpty ): ?string;
}
