<?php

namespace Kanopi\Components\Blocks\Model;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Standard methods to match and transform a Supported Tag by Name into other structured text content
 *
 * @package kanopi/components
 */
interface DomTransform {
	/**
	 * Check if the node matches this transform
	 *
	 * @param Crawler $_node Node to check
	 * @return bool
	 */
	public function matchesTransform( Crawler $_node ): bool;

	/**
	 * HTML Tag the transform manages
	 *
	 * @return string Tag name
	 */
	public function supportedTagName(): string;

	/**
	 * Transform a DOM node into an HTML string
	 *
	 * @param Crawler $_node       Incoming DOM Node
	 * @param bool    $_allowEmpty Whether to allow empty Nodes
	 *
	 * @return string Transformed HTML string
	 */
	public function transform( Crawler $_node, bool $_allowEmpty ): string;
}
