<?php

namespace Model\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\Paragraph;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The Paragraph DOM to Block transform
 *
 * @package kanopi/components
 */
class ParagraphBlockTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function transformData(): array {
		return [
			'Empty p tag'      => [ '<p></p>', '' ],
			'All spaces p tag' => [ '<p>   </p>', '' ],
			'P - Plain'        => [
				'<p>Normal <strong>Strong</strong> <em>Emphasis</em> <strong><em>Strong Emphasis</em></strong></p>',
				'<!-- wp:paragraph --><p>Normal <strong>Strong</strong> <em>Emphasis</em> <strong><em>Strong Emphasis</em></strong></p><!-- /wp:paragraph -->',
			],
			'P - Classes'      => [
				'<p class="class-1 Class2">Visit <em>this</em> <a href="https&#58;//some.com/url" target="_blank">Hyperlink</a></p>',
				'<!-- wp:paragraph {"className":"class-1 Class2"} --><p class="class-1 Class2">'
				. 'Visit <em>this</em> <a href="https://some.com/url" target="_blank">Hyperlink</a>'
				. '</p><!-- /wp:paragraph -->',
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
	public function testTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();
		$htmlCrawler->addHtmlContent( $_incoming );
		$this->assertEquals(
			$_expected,
			( new Paragraph() )->transform(
				$htmlCrawler->filter( 'p' )->first(),
				false
			)
		);
	}

	/**
	 * @test
	 */
	public function testVerifySupportsTag(): void {
		$this->assertEquals( 'p', ( new Paragraph() )->supportedTagName() );
	}
}
