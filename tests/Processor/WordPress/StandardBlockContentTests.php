<?php

namespace Processor\WordPress;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks;
use Kanopi\Components\Processor\WordPress\StandardBlockContent;
use Kanopi\Components\Transformers\Arrays;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for multiple transforms through the StandardBlockTest basic processor
 *
 * @package kanopi/components
 */
class StandardBlockContentTests extends TestCase {
	/**
	 * Test processor with several test transforms
	 *
	 * @return StandardBlockContent
	 */
	public function standardTestProcessor(): StandardBlockContent {
		$processor = new StandardBlockContent();
		$listItems = new Blocks\ListItem();
		$paragraph = new Blocks\Paragraph();

		$transforms = Arrays::from(
			[
				'paragraph'     => $paragraph,
				'blockquote'    => new Blocks\BlockQuote(),
				'orderedList'   => new Blocks\OrderedList(),
				'unorderedList' => new Blocks\UnorderedList(),
				'headingOne'    => new Blocks\HeadingOne(),
				'headingTwo'    => new Blocks\HeadingTwo(),
				'headingThree'  => new Blocks\HeadingThree(),
				'headingFour'   => new Blocks\HeadingFour(),
				'headingFive'   => new Blocks\HeadingFive(),
				'headingSix'    => new Blocks\HeadingSix(),
			]
		);

		// Recursive list registration
		$listItems->registerInnerTransform( $transforms->readIndex( 'orderedList' ) );
		$listItems->registerInnerTransform( $transforms->readIndex( 'unorderedList' ) );
		$transforms->readIndex( 'blockquote' )->registerInnerTransform( $paragraph );
		$transforms->readIndex( 'orderedList' )->registerInnerTransform( $listItems );
		$transforms->readIndex( 'unorderedList' )->registerInnerTransform( $listItems );

		foreach ( $transforms as $transform ) {
			$processor->registerInnerTransform( $transform );
		}

		return $processor;
	}

	/**
	 * @return array[]
	 */
	public function transformData(): array {
		return [
			'Empty content'                      => [ '<ul><li></li></ul><blockquote></blockquote><p></p>', '' ],
			'Nested Unordered Lists - Two Level' => [
				'<h1>Heading 1</h1>'
				. '		<p>Normal <strong>Strong</strong> <em>Emphasis</em> <strong><em>Strong Emphasis</em></strong></p>'
				. '				<h2>Heading 2</h2>'
				. '		<blockquote><p>Quote stuff</p><cite>Citation</cite></blockquote>'
				. '<ul><li>Item 1</li><li>Item 2<ul class="inner-class"><li>Inner 1'
				. '<ul class="inner-inner-class"><li>Item 3</li></ul></li></ul></li></ul>',
				'<!-- wp:heading {"level": 1} --><h1 class="wp-block-heading">Heading 1</h1><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>Normal <strong>Strong</strong> <em>Emphasis</em> <strong><em>Strong Emphasis</em></strong></p><!-- /wp:paragraph -->'
				. '<!-- wp:heading {"level": 2} --><h2 class="wp-block-heading">Heading 2</h2><!-- /wp:heading -->'
				. '<!-- wp:quote --><blockquote class="wp-block-quote">'
				. '<!-- wp:paragraph --><p>Quote stuff</p><!-- /wp:paragraph -->'
				. '<cite>Citation</cite>'
				. '</blockquote><!-- /wp:quote -->'
				. '<!-- wp:list --><ul>'
				. '<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2'
				. '<!-- wp:list {"className":"inner-class"} --><ul class="inner-class">'
				. '<!-- wp:list-item --><li>Inner 1'
				. '<!-- wp:list {"className":"inner-inner-class"} --><ul class="inner-inner-class">'
				. '<!-- wp:list-item --><li>Item 3</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->'
				. '</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->'
				. '</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider transformData
	 *
	 * @param string $_incoming Original DOM element string
	 * @param string $_expected Transformed DOM element as Blocks string
	 */
	public function testStandardBlockProcess( string $_incoming, string $_expected ): void {
		$output = $this->standardTestProcessor()->process( $_incoming );
		$this->assertEquals( $_expected, $output );
	}

	/**
	 * @test
	 */
	public function testVerifyListItemSupportsTag(): void {
		$this->assertEquals( 'body', $this->standardTestProcessor()->supportedTagName() );
	}
}
