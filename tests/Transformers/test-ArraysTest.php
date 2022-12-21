<?php

namespace Transformers;

use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase {
	/**
	 * @dataProvider provider
	 */
	public function testMethod($data)
	{
		$this->assertTrue($data);
	}

	public function provider()
	{
		return [
			'my named data' => [true],
			'my data'       => [true]
		];
	}
}
