<?php

namespace Transformers;

use PHPUnit\Framework\TestCase;

/**
 * Array test cases
 *
 * @package kanopi/components
 */
class ArraysTest extends TestCase {
	/**
	 * @dataProvider provider
	 *
	 * @param bool $data Incoming data
	 */
	public function testMethod( bool $data ) {
		$this->assertTrue( $data );
	}

	/**
	 * Test data provider
	 *
	 * @return array[]
	 */
	public function provider(): array {
		return [
			'my named data' => [ true ],
			'my data'       => [ true ],
		];
	}
}
