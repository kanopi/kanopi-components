<?php

namespace Kanopi\Components\Blocks\Model;

use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Standard method for Flow Elements
 *
 * @package kanopi/components
 */
trait FlowElementTransform {
	/**
	 * Checks the node contents based on whether empty content is allowed or skipped
	 *
	 * @param Crawler $_node       Node to check
	 * @param bool    $_allowEmpty Whether to allow empty nodes
	 *
	 * @return bool
	 */
	public function checkEmptyNodes( Crawler $_node, bool $_allowEmpty ): bool {
		return $_allowEmpty || 0 < $_node->filterXPath( $this->childNodePath() )->count();
	}

	/**
	 * Checks for the correct tag name
	 *
	 * @param string $_nodeName Node name type
	 *
	 * @return bool
	 */
	public function checkTagName( string $_nodeName ): bool {
		return $this->supportedTagName() === strtolower( $_nodeName );
	}

	/**
	 * Read the content of all child nodes into an HTML string, optionally pruning empty elements
	 *
	 * @param Crawler $_node       Containing node
	 * @param bool    $_allowEmpty Whether to allow empty child nodes
	 *
	 * @return string
	 */
	public function childNodeContentHtml( Crawler $_node, bool $_allowEmpty ): string {
		return Arrays::from(
			$_node->filterXPath( $this->childNodePath() )->each(
				function ( Crawler $_child ) use ( $_allowEmpty ) {
					$isChildElement = $_child->matches( '*' );

					// Remove empty class attributes from every DOMElement child
					if ( $isChildElement && '' === $_child->attr( 'class' ) ) {
						$_child->getNode( 0 )?->removeAttribute( 'class' );
					}

					$transform = $_child->matches( '*' )
						? $this->processInnerTransforms( $_child, $_allowEmpty )
						: null;

					return $transform ?? $_child->outerHtml();
				}
			)
		)->join( '' );
	}

	/**
	 * Child node page {supportedTagName}/node()
	 *
	 * @return string
	 */
	public function childNodePath(): string {
		return $this->supportedTagName() . '/node()';
	}

	/**
	 * {@inheritDoc}
	 */
	public function matchesTransform( Crawler $_node ): bool {
		return $this->checkTagName( $_node->nodeName() );
	}

	/**
	 * HTML Tag the transform manages
	 *
	 * @return string Tag name
	 */
	abstract public function supportedTagName(): string;

	/**
	 * Verify the current node has an expected class, case-insensitive match
	 *  - Allowed matching against one of many potential classes
	 *
	 * @param Crawler $_element         Element to check
	 * @param array   $_expectedClasses Classes to find
	 *
	 * @return bool
	 */
	public function verifyClassAttribute( Crawler $_element, array $_expectedClasses ): bool {
		// Manually split and match instead of HtmlPageCrawler hasClass(), which can only handle one class at a time
		$attribute = $_element->matches( '*' ) ? $_element->attr( 'class' ) : null;
		$classes   = preg_split( '/\s+/', $attribute ?? '' );
		$checkSet  = false !== $classes ? Arrays::from( $classes ) : Arrays::fresh();

		$checkSet->map(
			function ( $className ) {
				return strtolower( $className );
			}
		);

		return Arrays::from( $_expectedClasses )->any(
			function ( $_class ) use ( $checkSet ) {
				return $checkSet->containsValue( strtolower( $_class ) );
			}
		);
	}
}
