<?php

namespace Model\BlockProcessor\Dom\Blocks;

use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\ListItem;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\UnorderedList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The OrderedList DOM to Block transform
 *  - The ListItem inside OrderedList DOM to Block transform
 *
 * @package kanopi/components
 */
class UnorderedListBlockTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function listItemTransformData(): array {
		return [
			'Empty li tag'                        => [ '<ul><li></li></ul>', '' ],
			'Unordered List with Items - Plain'   => [
				'<ul><li>Item 1</li><li>Item 2</li></ul>',
				'<!-- wp:list --><ul>'
				. '<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->',
			],
			'Unordered List with Items - Classes' => [
				'<ul class="class1 Class2 class-3"><li><strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em></li><li>Item 2</li></ul>',
				'<!-- wp:list {"className":"class1 Class2 class-3"} --><ul class="class1 Class2 class-3">'
				. '<!-- wp:list-item --><li><strong>Item</strong> 1 <a href="/path/to">link</a>, with <em>emphasis</em></li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->',
			],
			'Nested Unordered Lists - One Level'  => [
				'<ul><li>Item 1</li><li>Item 2<ul class="inner-class"><li>Inner 1</li></ul></li></ul>',
				'<!-- wp:list --><ul>'
				. '<!-- wp:list-item --><li>Item 1</li><!-- /wp:list-item -->'
				. '<!-- wp:list-item --><li>Item 2'
				. '<!-- wp:list {"className":"inner-class"} --><ul class="inner-class">'
				. '<!-- wp:list-item --><li>Inner 1</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->'
				. '</li><!-- /wp:list-item -->'
				. '</ul><!-- /wp:list -->',
			],
			'Nested Unordered Lists - Two Level'  => [
				'<ul><li>Item 1</li><li>Item 2<ul class="inner-class"><li>Inner 1<ul class="inner-inner-class"><li>Item 3</li></ul></li></ul></li></ul>',
				'<!-- wp:list --><ul>'
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
	 * @return array[]
	 */
	public function unorderedBaseTransformData(): array {
		return [
			'Empty ul tag'             => [ '<ul></ul>', '' ],
			'Unordered List - Plain'   => [
				'<ul><li>Item 1</li><li>Item 2</li></ul>',
				'<!-- wp:list --><ul><li>Item 1</li><li>Item 2</li></ul><!-- /wp:list -->',
			],
			'Unordered List - Classes' => [
				'<ul class="class1 Class2 class-3"><li>Item 1</li><li>Item 2</li></ul>',
				'<!-- wp:list {"className":"class1 Class2 class-3"} --><ul class="class1 Class2 class-3">'
				. '<li>Item 1</li>'
				. '<li>Item 2</li>'
				. '</ul><!-- /wp:list -->',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider listItemTransformData
	 *
	 * @param string $_incoming Original DOM element string
	 * @param string $_expected Transformed DOM element as Blocks string
	 */
	public function testListItemTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();
		$lists       = new UnorderedList();
		$listItems   = new ListItem();

		$htmlCrawler->addHtmlContent( $_incoming );
		$lists->registerInnerTransform( $listItems );
		$listItems->registerInnerTransform( $lists );

		$this->assertEquals(
			$_expected,
			$lists->transform(
				$htmlCrawler->filter( 'ul' )->first(),
				false
			)
		);
	}

	/**
	 * @test
	 * @dataProvider unorderedBaseTransformData
	 *
	 * @param string $_incoming Original DOM element string
	 * @param string $_expected Transformed DOM element as Blocks string
	 */
	public function testUnorderedBaseTransform( string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();

		$htmlCrawler->addHtmlContent( $_incoming );
		$this->assertEquals(
			$_expected,
			( new UnorderedList() )->transform(
				$htmlCrawler->filter( 'ul' )->first(),
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
		$this->assertEquals( 'ul', ( new UnorderedList() )->supportedTagName() );
	}
}
