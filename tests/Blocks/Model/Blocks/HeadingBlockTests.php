<?php

namespace Blocks\Model\Blocks;

use Kanopi\Components\Blocks\Model\RecursiveDomTransform;
use Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Blocks\{Kanopi\Components\Blocks\Model\Blocks\HeadingFive,
	Kanopi\Components\Blocks\Model\Blocks\HeadingFour,
	Kanopi\Components\Blocks\Model\Blocks\HeadingOne,
	Kanopi\Components\Blocks\Model\Blocks\HeadingSix,
	Kanopi\Components\Blocks\Model\Blocks\HeadingThree,
	Kanopi\Components\Blocks\Model\Blocks\HeadingTwo};
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Unit and Integration tests for:
 *  - The Heading DOM to Block transform
 *
 * @package kanopi/components
 */
class HeadingBlockTests extends TestCase {
	/**
	 * @return array[]
	 */
	public function transformData(): array {
		return [
			'Empty h1 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingOne(), '<h1></h1>', '' ],
			'Empty h2 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingTwo(), '<h2></h2>', '' ],
			'Empty h3 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingThree(), '<h3></h3>', '' ],
			'Empty h4 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingFour(), '<h4></h4>', '' ],
			'Empty h5 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingFive(), '<h5></h5>', '' ],
			'Empty h6 tag' => [ new \Kanopi\Components\Blocks\Model\Blocks\HeadingSix(), '<h6></h6>', '' ],
			'h1 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingOne(),
				'<h1>Heading 1</h1>',
				'<!-- wp:heading {"level": 1} --><h1 class="wp-block-heading">Heading 1</h1><!-- /wp:heading -->',
			],
			'h2 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingTwo(),
				'<h2>Heading 2</h2>',
				'<!-- wp:heading {"level": 2} --><h2 class="wp-block-heading">Heading 2</h2><!-- /wp:heading -->',
			],
			'h3 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingThree(),
				'<h3>Heading 3</h3>',
				'<!-- wp:heading {"level": 3} --><h3 class="wp-block-heading">Heading 3</h3><!-- /wp:heading -->',
			],
			'h4 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingFour(),
				'<h4>Heading 4</h4>',
				'<!-- wp:heading {"level": 4} --><h4 class="wp-block-heading">Heading 4</h4><!-- /wp:heading -->',
			],
			'h5 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingFive(),
				'<h5>Heading 5</h5>',
				'<!-- wp:heading {"level": 5} --><h5 class="wp-block-heading">Heading 5</h5><!-- /wp:heading -->',
			],
			'h6 - Plain'   => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingSix(),
				'<h6>Heading 6</h6>',
				'<!-- wp:heading {"level": 6} --><h6 class="wp-block-heading">Heading 6</h6><!-- /wp:heading -->',
			],
			'h1 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingOne(),
				'<h1 class="class1 Class2 class-3">Heading 1</h1>',
				'<!-- wp:heading {"level": 1,"className":"class1 Class2 class-3"} --><h1 class="wp-block-heading class1 Class2 class-3">Heading 1</h1><!-- /wp:heading -->',
			],
			'h2 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingTwo(),
				'<h2 class="class1 Class2 class-3">Heading 2</h2>',
				'<!-- wp:heading {"level": 2,"className":"class1 Class2 class-3"} --><h2 class="wp-block-heading class1 Class2 class-3">Heading 2</h2><!-- /wp:heading -->',
			],
			'h3 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingThree(),
				'<h3 class="class1 Class2 class-3">Heading 3</h3>',
				'<!-- wp:heading {"level": 3,"className":"class1 Class2 class-3"} --><h3 class="wp-block-heading class1 Class2 class-3">Heading 3</h3><!-- /wp:heading -->',
			],
			'h4 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingFour(),
				'<h4 class="class1 Class2 class-3">Heading 4</h4>',
				'<!-- wp:heading {"level": 4,"className":"class1 Class2 class-3"} --><h4 class="wp-block-heading class1 Class2 class-3">Heading 4</h4><!-- /wp:heading -->',
			],
			'h5 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingFive(),
				'<h5 class="class1 Class2 class-3">Heading 5</h5>',
				'<!-- wp:heading {"level": 5,"className":"class1 Class2 class-3"} --><h5 class="wp-block-heading class1 Class2 class-3">Heading 5</h5><!-- /wp:heading -->',
			],
			'h6 - Classes' => [
				new \Kanopi\Components\Blocks\Model\Blocks\HeadingSix(),
				'<h6 class="class1 Class2 class-3">Heading 6</h6>',
				'<!-- wp:heading {"level": 6,"className":"class1 Class2 class-3"} --><h6 class="wp-block-heading class1 Class2 class-3">Heading 6</h6><!-- /wp:heading -->',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider transformData
	 *
	 * @param RecursiveDomTransform $_headingLevel Heading transform to test
	 * @param string                $_incoming     Original DOM element string
	 * @param string                $_expected     Transformed DOM element as Blocks string
	 */
	public function testTransform( RecursiveDomTransform $_headingLevel, string $_incoming, string $_expected ): void {
		$htmlCrawler = new Crawler();
		$htmlCrawler->addHtmlContent( "<html><body>{$_incoming}</body></html>" );
		$this->assertEquals(
			$_expected,
			$_headingLevel->transform(
				$htmlCrawler->filterXPath( '//body' )->children()->first(),
				false
			)
		);
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingOneTag(): void {
		$this->assertEquals( 'h1', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingOne() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingTwoTag(): void {
		$this->assertEquals( 'h2', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingTwo() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingThreeTag(): void {
		$this->assertEquals( 'h3', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingThree() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingFourTag(): void {
		$this->assertEquals( 'h4', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingFour() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingFiveTag(): void {
		$this->assertEquals( 'h5', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingFive() )->supportedTagName() );
	}

	/**
	 * @test
	 */
	public function testVerifySupportsHeadingSixTag(): void {
		$this->assertEquals( 'h6', ( new \Kanopi\Components\Blocks\Model\Blocks\HeadingSix() )->supportedTagName() );
	}
}
