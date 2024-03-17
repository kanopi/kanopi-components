<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom;

use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Support for Standard/Core WordPress Block transformations
 *  - Fulfills the DomTransform interface
 *  - Only for basic, single level tags like List Item and Paragraph
 *
 * @package kanopi/components
 */
trait StandardBlock {
	use FlowElementTransform;

	/**
	 * The block name, like wp:paragraph from the Block Editor metadata comment
	 *
	 * @return string
	 */
	abstract protected function blockName(): string;

	/**
	 * The block tag name, like p, ul, or img for the outer DOM element
	 *
	 * @return string
	 */
	abstract protected function blockTagName(): string;

	/**
	 * Associative array of Old class to New class
	 *  - For instance:
	 *      [
	 *          'OldClass1' => 'is-style-new-class',
	 *          'OldClass2' => 'other-new-class',
	 *      ]
	 *
	 * @return array
	 */
	protected function classMapping(): array {
		return [];
	}

	/**
	 * List of default classes for the block outer DOM element
	 *  - This is added only to the Outer DOM element, not the Block Metadata
	 *  - Empty if there are none
	 *  - For most blocks, something like: 'wp-block-my-block-name'
	 *
	 * @return string
	 */
	protected function defaultClass(): string {
		return '';
	}

	/**
	 * Filter the applied attributes, these are joined at the end
	 *  - Override this function to add elements to the Block metadata or DOM attribute list for the outer tag
	 *
	 * @param Crawler $_node            Processed node
	 * @param Arrays  $_blockAttributes Block attributes
	 * @param Arrays  $_domAttributes   DOM Attributes
	 */
	protected function filterNodeAndAttributes(
		Crawler $_node,
		Arrays $_blockAttributes,
		Arrays $_domAttributes
	): void {}


	/**
	 * Check the nodes class list and replace select classes
	 *
	 * @param Crawler $_node Processed node
	 *
	 * @return string
	 */
	protected function mapClasses( Crawler $_node ): string {
		$classes     = Arrays::fresh();
		$nodeClasses = $_node->attr( 'class' ) ?? '';

		$classes->addMaybe( $nodeClasses, ! empty( $nodeClasses ) );

		foreach ( $this->classMapping() as $original => $replacement ) {
			$nodeHasOriginal = $this->verifyClassAttribute( $_node, [ $original ] );

			$classes->addMaybe( $replacement, $nodeHasOriginal );
		}

		return $classes->join( ' ' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function matchesTransform( Crawler $_node ): bool {
		$classMapping = Arrays::from( $this->classMapping() );
		$classesValid = $classMapping->isEmpty() || $this->verifyClassAttribute( $_node, $classMapping->keys() );

		return $classesValid && $this->checkTagName( $_node->nodeName() );
	}

	/**
	 * Transform a Node into a Block
	 *
	 * @param Crawler $_node       Node to transform
	 * @param bool    $_allowEmpty Whether to allow empty Nodes
	 *
	 * @return string
	 */
	protected function processBlockTransformation( Crawler $_node, bool $_allowEmpty ): string {
		$blockAttributes = Arrays::fresh();
		$domAttributes   = Arrays::fresh();
		$domClasses      = Arrays::fresh();
		$nodeClasses     = $this->mapClasses( $_node );
		$hasNodeClasses  = ! empty( $nodeClasses );
		$nodeId          = $_node->attr( 'id' );

		// Process DOM node classes into Block attributes and mapped DOM Classes
		$blockAttributes->addMaybe( sprintf( '"className":"%s"', $nodeClasses ), $hasNodeClasses );
		$domClasses->addMaybe( $this->defaultClass(), ! empty( $this->defaultClass() ) );
		$domClasses->addMaybe( $nodeClasses, $hasNodeClasses );
		$domAttributes->addMaybe(
			sprintf( 'class="%s"', $domClasses->join( ' ' ) ),
			! $domClasses->isEmpty()
		);
		$domAttributes->addMaybe( sprintf( 'id="%s"', $nodeId ), ! empty( $nodeId ) );

		// Override this if you need additional DOM attributes or Block Metadata
		$this->filterNodeAndAttributes( $_node, $blockAttributes, $domAttributes );

		$nodeContent    = $this->childNodeContentHtml( $_node, $_allowEmpty );
		$useNodeContent = $_allowEmpty || ! empty( trim( $nodeContent ) );

		return $useNodeContent ? $this->readFinalBlock( $blockAttributes, $domAttributes, $nodeContent ) : '';
	}

	/**
	 * Read the completed/final block string after all adjustments
	 *  - Override if the template() ordering or number of arguments was changed
	 *
	 * @param Arrays $_blockAttributes Block attributes
	 * @param Arrays $_domAttributes   DOM Attributes
	 * @param string $_nodeContent     Inner content for the new node
	 *
	 * @return string
	 */
	protected function readFinalBlock(
		Arrays $_blockAttributes,
		Arrays $_domAttributes,
		string $_nodeContent
	): string {
		$tagAttributes = $_domAttributes->join( ' ' );
		$blockMetaData = $_blockAttributes->isEmpty() ? '' : sprintf( '{%s} ', $_blockAttributes->join() );

		return sprintf(
			$this->template(),
			$this->blockName(),
			$blockMetaData,
			$this->blockTagName(),
			! empty( $tagAttributes ) ? ( ' ' . $tagAttributes ) : '',
			$_nodeContent
		);
	}

	/**
	 * DOMNode to WordPress Block Template
	 *  - Override for a different layout
	 *  - Default positions:
	 *      1. Block name, i.e. wp:paragraph
	 *      2. Block metadata
	 *      3. Outer DOM Tag Name
	 *      4. Outer DOM Element attributes
	 *      5. DOM Element Inner HTML
	 *
	 * @return string
	 */
	protected function template(): string {
		return '<!-- %1$s %2$s--><%3$s%4$s>%5$s</%3$s><!-- /%1$s -->';
	}

	/**
	 * Transform a DOM node into an HTML string
	 *
	 * @param Crawler $_node       Incoming DOM Node
	 * @param bool    $_allowEmpty Whether to allow empty Nodes
	 *
	 * @return string Transformed HTML string
	 */
	public function transform( Crawler $_node, bool $_allowEmpty ): string {
		return $this->matchesTransform( $_node ) && $this->checkEmptyNodes( $_node, $_allowEmpty )
			? $this->processBlockTransformation( $_node, $_allowEmpty )
			: '';
	}
}
