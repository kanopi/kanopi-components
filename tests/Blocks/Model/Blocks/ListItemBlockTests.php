<?php

namespace Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\Blocks\ListItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The ListItem DOM to Block transform
 *
 * @package kanopi/components
 */
class ListItemBlockTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function listItemTransformData(): array {
		return [
			'Empty li tag'        => [ '<li></li>', '' ],
			'Space filled li tag' => [ '<li>   </li>', '' ],
			'List Item - Plain'   => [
				'<li>Item 1</li>',
				'<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->',
			],
			'List Item - Classes' => [
				'<li class="class1 Class2 class-3"><strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em></li>',
				'<!-- wp:list-item {"className":"class1 Class2 class-3"} --><li class="class1 Class2 class-3">'
				. '<strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em>'
				. '</li><!-- /wp:list-item -->',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider listItemTransformData
	 * @param string $_incoming Original DOM element string
	 * @param string $_expected Transformed DOM element as Blocks string
	 */
	public function testListItemTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();
		$htmlCrawler->addHtmlContent( $_incoming );

		$this->assertEquals(
			$_expected,
			( new ListItem() )->transform(
				$htmlCrawler->filter( 'li' )->first(),
				false
			)
		);
	}

	/**
	 * @test
	 */
	public function testVerifyListItemSupportsTag(): void {
		$this->assertEquals( 'li', ( new ListItem() )->supportedTagName() );
	}
}
