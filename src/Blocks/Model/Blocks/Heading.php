<?php

namespace Kanopi\Components\Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\ParentTransform;
use Kanopi\Components\Blocks\Model\StandardBlock;
use Kanopi\Components\Transformers\Arrays;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Pattern to transform a hX tag into a WordPress Heading Block with requested level
 *
 * @package kanopi/components
 */
trait Heading {
	use StandardBlock;
	use ParentTransform;

	/**
	 * {@inheritDoc}
	 */
	protected function blockName(): string {
		return 'wp:heading';
	}

	/**
	 * {@inheritDoc}
	 *  - Checks for a valid tag level, defaults to h2 if invalid
	 */
	protected function blockTagName(): string {
		return sprintf( 'h%s', $this->validateLevel( $this->headingTransformLevel() ) );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function defaultClass(): string {
		return 'wp-block-heading';
	}

	/**
	 * What level we are setting, between 1 and 6, defaults to 2 if invalid
	 *
	 * @return int Heading tag name level
	 */
	abstract public function headingTransformLevel(): int;

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
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
		// Proxy the existing block attributes into placeholder attributes array, prepend the heading level
		$blockAttributes = Arrays::from(
			[ sprintf( '"level": %d', $this->validateLevel( $this->headingTransformLevel() ) ) ]
		)->append( $_blockAttributes->toArray() );

		// Clear the original block attributes and add the revised set of attributes
		$_blockAttributes->empty()->append( $blockAttributes->toArray() );
	}
	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

	/**
	 * Validate a valid heading level, defaults to 2
	 *
	 * @param int $_level Incoming heading level
	 *
	 * @return int
	 */
	protected function validateLevel( int $_level ): int {
		return in_array( $_level, [ 1, 2, 3, 4, 5, 6 ], true ) ? $_level : 2;
	}

	/**
	 * @inheritDoc
	 */
	abstract public function supportedTagName(): string;
}
