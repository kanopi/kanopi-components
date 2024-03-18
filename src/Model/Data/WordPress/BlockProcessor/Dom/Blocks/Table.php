<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\FlowElementTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\ParentTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\RecursiveDomTransform;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Transform a table tag into a WordPress Table Block
 *
 * @package kanopi/components
 */
class Table implements RecursiveDomTransform {
	use FlowElementTransform;
	use ParentTransform;

	/**
	 * DOMNode to WordPress Block Template with Class Style
	 */
	const TEMPLATE = '<!-- wp:table --><figure class="wp-block-table"><table><tbody>%1$s</tbody></table></figure><!-- /wp:table -->';

	/**
	 * Cleans up the incoming table so that it will match what is generated from Core
	 *
	 * @param HtmlPageCrawler $_node Processed node
	 *
	 * @return void
	 */
	protected function cleanUpTables( HtmlPageCrawler $_node ): void {
		$_node->children()->children( 'th,td' )->removeAttribute( 'align' );
		$_node->children()->children( 'th,td' )->removeAttribute( 'valign' );

		// Remove paragraph tags, maintaining inner content, joining with br tags
		$_node->children()->children( 'th,td' )->each(
			function ( Crawler $_child ) {
				$child      = HtmlPageCrawler::create( $_child );
				$_paragraph = $child->children( 'p' );
				$_nodeChild = $child->filterXPath( '*/node()' );
				if ( count( $_paragraph ) === count( $_nodeChild ) ) :
					$paraArray = Arrays::from(
						$_paragraph->each(
							function ( Crawler $_paraContent ) {
								$paraContent = HtmlPageCrawler::create( $_paraContent );
								return $paraContent->getInnerHtml();
							}
						)
					);

					$output = $paraArray->join( '<br /><br />' );
					$child->setInnerHtml( $output );
				endif;
			}
		);
	}

	/**
	 * Transform a Node into a Block
	 *
	 * @param HtmlPageCrawler $_node       Node to transform
	 * @param bool            $_allowEmpty Whether to allow empty Nodes
	 *
	 * @return string
	 */
	protected function processBlockTransformation( HtmlPageCrawler $_node, bool $_allowEmpty ): string {
		// Process removal of inner paragraph tags, which are not supported in Table blocks
		$this->cleanUpTables( $_node );
		$nodeContent = $this->childNodeContentHtml( $_node, $_allowEmpty );

		return $_allowEmpty || ! empty( $nodeContent )
			? sprintf(
				self::TEMPLATE,
				$nodeContent
			)
			: '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportedTagName(): string {
		return 'table';
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform( Crawler $_node, bool $_allowEmpty ): string {
		$node = HtmlPageCrawler::create( $_node );

		return $this->matchesTransform( $_node ) && $this->checkEmptyNodes( $node, $_allowEmpty )
			? $this->processBlockTransformation( $node, $_allowEmpty )
			: '';
	}
}
