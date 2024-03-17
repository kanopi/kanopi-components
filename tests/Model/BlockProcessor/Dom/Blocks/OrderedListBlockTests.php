<?php

namespace Model\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\ListItem;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\OrderedList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The OrderedList DOM to Block transform
 *  - The ListItem inside OrderedList DOM to Block transform
 *
 * @package kanopi/components
 */
class OrderedListBlockTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function listItemTransformData(): array {
		return [
			'Empty li tag'                      => [ '<ol><li></li></ol>', '' ],
			'Ordered List with Items - Plain'   => [
				'<ol><li>Item 1</li><li>Item 2</li></ol>',
				'<!-- wp:list {"ordered":true} --><ol>'
				. '<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2</li><!-- /wp:list-item -->'
				. '</ol><!-- /wp:list -->',
			],
			'Ordered List with Items - Classes' => [
				'<ol class="class1 Class2 class-3"><li><strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em></li><li>Item 2</li></ol>',
				'<!-- wp:list {"ordered":true,"className":"class1 Class2 class-3"} --><ol class="class1 Class2 class-3">'
				. '<!-- wp:list-item --><li><strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em></li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2</li><!-- /wp:list-item -->'
				. '</ol><!-- /wp:list -->',
			],
			'Nested Ordered Lists'              => [
				'<ol><li>Item 1</li><li>Item 2<ol class="inner-class" start="11"><li>Inner 1</li></ol></li></ol>',
				'<!-- wp:list {"ordered":true} --><ol>'
				. '<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2'
				. '<!-- wp:list {"ordered":true,"className":"inner-class","start":11} --><ol class="inner-class" start="11">'
				. '<!-- wp:list-item --><li>Inner 1</li><!-- /wp:list-item -->'
				. '</ol><!-- /wp:list -->'
				. '</li><!-- /wp:list-item -->'
				. '</ol><!-- /wp:list -->',
			],
		];
	}

	/**
	 * @return array[]
	 */
	public function orderedBaseTransformData(): array {
		return [
			'Empty ol tag'                     => [ '<ol></ol>', '' ],
			'Ordered List - Plain'             => [
				'<ol><li>Item 1</li><li>Item 2</li></ol>',
				'<!-- wp:list {"ordered":true} --><ol><li>Item 1</li><li>Item 2</li></ol><!-- /wp:list -->',
			],
			'Ordered List - Classes'           => [
				'<ol class="class1 Class2 class-3"><li>Item 1</li><li>Item 2</li></ol>',
				'<!-- wp:list {"ordered":true,"className":"class1 Class2 class-3"} --><ol class="class1 Class2 class-3">'
				. '<li>Item 1</li>'
				. '<li>Item 2</li>'
				. '</ol><!-- /wp:list -->',
			],
			'Ordered List - Start'             => [
				'<ol start="3"><li>Item 3</li><li>Item 4</li></ol>',
				'<!-- wp:list {"ordered":true,"start":3} --><ol start="3">'
				. '<li>Item 3</li>'
				. '<li>Item 4</li>'
				. '</ol><!-- /wp:list -->',
			],
			'Ordered List - Classes and Start' => [
				'<ol start="3" class="class-3 Class4"><li>Item 3</li><li>Item 4</li></ol>',
				'<!-- wp:list {"ordered":true,"className":"class-3 Class4","start":3} --><ol class="class-3 Class4" start="3">'
				. '<li>Item 3</li>'
				. '<li>Item 4</li>'
				. '</ol><!-- /wp:list -->',
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
		$lists       = new OrderedList();
		$listItems   = new ListItem();

		$htmlCrawler->addHtmlContent( $_incoming );
		$lists->registerInnerTransform( $listItems );
		$listItems->registerInnerTransform( $lists );

		$this->assertEquals(
			$_expected,
			$lists->transform(
				$htmlCrawler->filter( 'ol' )->first(),
				false
			)
		);
	}

	/**
	 * @test
	 * @dataProvider orderedBaseTransformData
	 * @param string $_incoming Original DOM element string
	 * @param string $_expected Transformed DOM element as Blocks string
	 */
	public function testOrderedBaseTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();

		$htmlCrawler->addHtmlContent( $_incoming );
		$this->assertEquals(
			$_expected,
			( new OrderedList() )->transform(
				$htmlCrawler->filter( 'ol' )->first(),
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

	/**
	 * @test
	 */
	public function testVerifyOrderedListSupportsTag(): void {
		$this->assertEquals( 'ol', ( new OrderedList() )->supportedTagName() );
	}
}
