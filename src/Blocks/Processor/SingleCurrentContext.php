<?php

namespace Kanopi\Components\Blocks\Processor;

use DOMElement;
use DOMNode;
use DOMNodeList;
use Kanopi\Components\Blocks\Model\Contexts\ContextStatus;
use Kanopi\Components\Blocks\Model\TransformContext;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Common implementation for the SingleContext interface
 *
 * @package kanopi/components
 */
trait SingleCurrentContext {
	/**
	 * Set of TransformContexts for processing
	 *
	 * @var array
	 */
	protected array $availableContexts = [];
	/**
	 * @var TransformContext|null
	 */
	protected ?TransformContext $currentContext = null;

	/**
	 * Clears the current top-level context
	 *
	 * @return void
	 */
	public function clearCurrentContext(): void {
		$this->currentContext = null;
	}

	/**
	 * Whether there is a currently assigned outer context
	 *
	 * @return bool
	 */
	public function hasCurrentContext(): bool {
		return ! empty( $this->currentContext );
	}

	/**
	 * Process an existing context
	 *  - Add the current node content to the context
	 *
	 * @param Crawler $_node         Current node
	 * @param Arrays  $_outputStream Current output stream array
	 *
	 * @return ContextStatus Context status
	 */
	public function processCurrentContext( Crawler $_node, Arrays $_outputStream ): ContextStatus {
		$status = $this->currentContext->tryEnd( $_node );

		if ( $status->isEnded() ) {
			$_outputStream->append( [ $this->currentContext->transform( $_node, false ) ] );
			$this->clearCurrentContext();
		}

		return $status;
	}

	/**
	 * Check if a node starts a new context, setting it as the current context
	 *
	 * @param Crawler $_node Current node
	 *
	 * @return ContextStatus Context status
	 */
	public function processStartContext( Crawler $_node ): ContextStatus {
		$status = ContextStatus::none();

		foreach ( $this->availableContexts as $context ) {
			$status = $context->tryStart( $_node );
			if ( $status->isStarted() ) {
				$this->currentContext = $context;
				break;
			}
		}

		return $status;
	}

	/**
	 * Action after all contexts are processed, allows interception/filtering of the process in implementing classes
	 * - To skip standard inner transform processing, perform actions then set the current node status to used
	 *
	 * @param Crawler       $_node   Current process node
	 * @param ContextStatus $_status Current process status
	 *
	 * @return ContextStatus Revised status
	 */
	protected function postContextAction( Crawler $_node, ContextStatus $_status ): ContextStatus {
		return $_status;
	}

	/**
	 * Register a context for internal use
	 *  - Contexts are processed in FIFO order (First In, First Out/processed)
	 *
	 * @param TransformContext $_transform Context to register
	 *
	 * @return void
	 */
	public function registerContext( TransformContext $_transform ): void {
		$this->availableContexts[] = $_transform;
	}

	/**
	 * Finds and removes all empty children in a given set of tag names
	 *  - These typically occur from unwrapping an incorrectly nested DOM structure
	 *  - Defaults to 'p' only, override with a larger set of tags when required
	 *
	 * @param Crawler     $_node Crawler at the body element of a document
	 * @param Arrays|null $_tags Set of tags to check for empty content
	 */
	protected function removeEmptyChildrenByTagName( Crawler $_node, ?Arrays $_tags = null ): void {
		$pathFilters = Arrays::from( [] );
		$tags        = ! empty( $_tags ) ? $_tags : Arrays::from( [ 'p' ] );

		foreach ( $tags->toArray() as $tag ) {
			$pathFilters->append( [ "descendant-or-self::{$tag}" ] );
		}

		$pathFilter = $pathFilters->join( '|' );

		// Only process children if there are nodes, avoid internal exception from Crawler
		if ( ! empty( $pathFilter ) && 0 < $_node->count() ) {
			$_node->filterXPath( $pathFilter )?->each(
				/**
				 * Remove each matched, empty node
				 *
				 * @var Crawler $_matchNode
				 */
				function ( Crawler $_matchNode ) {
					if (
						is_a( $_matchNode, DOMNode::class )
						&& empty( trim( $_matchNode->html( '' ) ) )
						&& $_matchNode->parentNode instanceof DOMElement
					) {
						$_matchNode->parentNode->removeChild( $_matchNode );
					}

					$_matchNode->clear();
				}
			);
		}

		// Remove remaining whitespace left over between removed blocks and converted spacing
		if ( empty( trim( $_node->html( '' ) ) ) ) {
			$this->replaceNodes( $_node, '' );
		}
	}

	/**
	 * Replace each element in the set of matched elements with the provided new content
	 *
	 * @param Crawler                    $_node   Node to replace
	 * @param string|DOMNode|DOMNodeList $content Replacement content
	 *
	 * @return Crawler Updated, replaced node for chaining
	 */
	public function replaceNodes( Crawler $_node, string|DOMNode|DOMNodeList $content ): Crawler {
		$content      = new Crawler( $content );
		$replacements = [];

		/**
		 * Process the original set of nodes
		 *
		 * @var DOMNode $node
		 */
		foreach ( $_node as $node ) {
			$parent   = $node->parentNode;
			$nextNode = $node->nextSibling;

			/**
			 * Process each replacement node, inserting them sequentially
			 *
			 * @var int|string|null $newIndex
			 * @var DOMNode         $replacement
			 */
			foreach ( $content as $newIndex => $replacement ) {
				$replacement = $node->ownerDocument->importNode( $replacement, true );

				0 === $newIndex
					? $parent->replaceChild( $replacement, $node )
					: $parent->insertBefore( $replacement, $nextNode );

				$replacements[] = $replacement;
			}
		}

		$content->clear();
		$content->add( $replacements );

		return $_node;
	}

	/**
	 * Process the current contexts collection
	 *
	 * @param Crawler  $_node       Current node (typically unused, but passed for nested interface compliance)
	 * @param iterable $_collection Collection of nodes to process
	 * @param bool     $_allowEmpty Whether to allow empty tags
	 *
	 * @return string
	 */
	public function transformContextCollection( Crawler $_node, iterable $_collection, bool $_allowEmpty ): string {
		$output = Arrays::from( [] );

		/**
		 * Process all nodes, text/comments are ignored from input
		 *
		 * @var Crawler $child
		 */
		foreach ( $_collection as $_child ) {
			$child = new Crawler( $_child );
			$this->removeEmptyChildrenByTagName( $child );

			$status = $this->hasCurrentContext()
				? $this->processCurrentContext( $child, $output )
				: $this->processStartContext( $child );

			$status = $this->postContextAction( $child, $status );

			if ( ! $status->isCurrentNodeUsed() ) {
				$transform = $child->matches( '*' )
					? $this->processInnerTransforms( $child, false )
					: null;

				$output->append( [ $transform ?? $child->outerHtml() ] );
			}
		}

		// Write any unfinished context at the contents' end
		if ( $this->hasCurrentContext() ) {
			$output->append( [ $this->currentContext->transform( $_node, $_allowEmpty ) ] );
		}

		return $output->join( '' );
	}
}
