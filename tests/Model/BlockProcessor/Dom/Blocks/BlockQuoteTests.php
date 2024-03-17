<?php

namespace Model\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\BlockQuote;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\Paragraph;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The Paragraph DOM to Block transform
 *
 * @package kanopi/components
 */
class BlockQuoteTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function transformData(): array {
		return [
			'Empty blockquote tag'           => [ '<blockquote></blockquote>', '' ],
			'Blockquote - Plain'             => [
				'<blockquote><p>Quote stuff</p></blockquote>',
				'<!-- wp:quote --><blockquote class="wp-block-quote">'
				. '<!-- wp:paragraph --><p>Quote stuff</p><!-- /wp:paragraph -->'
				. '</blockquote><!-- /wp:quote -->',
			],
			'Blockquote - Citation'          => [
				'<blockquote><p>Quote stuff</p><cite>Citation</cite></blockquote>',
				'<!-- wp:quote --><blockquote class="wp-block-quote">'
				. '<!-- wp:paragraph --><p>Quote stuff</p><!-- /wp:paragraph -->'
				. '<cite>Citation</cite>'
				. '</blockquote><!-- /wp:quote -->',
			],
			'Blockquote - Classes'           => [
				'<blockquote class="class-one class2"><p>Quote stuff</p></blockquote>',
				'<!-- wp:quote {"className":"class-one class2"} -->'
				. '<blockquote class="wp-block-quote class-one class2">'
				. '<!-- wp:paragraph --><p>Quote stuff</p><!-- /wp:paragraph -->'
				. '</blockquote><!-- /wp:quote -->',
			],
			'Blockquote - Paragraph Classes' => [
				'<blockquote class="class-one class2"><p class="class-three class4">Quote stuff</p></blockquote>',
				'<!-- wp:quote {"className":"class-one class2"} -->'
				. '<blockquote class="wp-block-quote class-one class2">'
				. '<!-- wp:paragraph {"className":"class-three class4"} -->'
				. '<p class="class-three class4">Quote stuff</p>'
				. '<!-- /wp:paragraph -->'
				. '</blockquote><!-- /wp:quote -->',
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
	public function testBlockQuoteTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();
		$transform   = new BlockQuote();
		$htmlCrawler->addHtmlContent( $_incoming );
		$transform->registerInnerTransform( new Paragraph() );

		$this->assertEquals(
			$_expected,
			$transform->transform(
				$htmlCrawler->filter( 'blockquote' )->first(),
				false
			)
		);
	}

	/**
	 * @test
	 */
	public function testVerifySupportsBlockQuoteTag(): void {
		$this->assertEquals( 'blockquote', ( new BlockQuote() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsParagraphTag(): void {
		$this->assertEquals( 'p', ( new Paragraph() )->supportedTagName() );
	}
}
