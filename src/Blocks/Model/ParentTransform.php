<?php

namespace Kanopi\Components\Blocks\Model;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Functionality for Parent Elements which contain other transformed Child Elements
 *
 * @package kanopi/components
 */
trait ParentTransform {
	/**
	 * 2D array of inner DomTransforms, segmented by tag name
	 *
	 * @var array
	 */
	protected array $innerTransforms = [];

	/**
	 * Register an additional inner transformation
	 *  - Segments transforms by supportedTagName()
	 *  - Applied in the order they are added, first match wins
	 *
	 * @param DomTransform $_transform Transform to register
	 * @return void
	 */
	public function registerInnerTransform( DomTransform $_transform ): void {
		$this->innerTransforms[ $_transform->supportedTagName() ][] = $_transform;
	}

	/**
	 * Run the node through all registered inner transforms
	 *
	 * @param Crawler $_node       Node to process
	 * @param bool    $_allowEmpty Whether empty inner tags are allowed
	 * @return string|null
	 */
	public function processInnerTransforms( Crawler $_node, bool $_allowEmpty ): ?string {
		$transformation = null;

		/**
		 * @var DomTransform $transform
		 */
		foreach ( $this->innerTransforms[ $_node->nodeName() ] ?? [] as $transform ) {
			if ( $transform->matchesTransform( $_node ) ) {
				$transformation = $transform->transform( $_node, $_allowEmpty );
				break;
			}
		}

		return $transformation;
	}
}
