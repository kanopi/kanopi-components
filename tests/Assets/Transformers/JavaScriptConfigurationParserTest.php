<?php

namespace Assets\Transformers;

use Kanopi\Components\Assets\Transformers\JavaScriptConfigurationParser;
use Kanopi\Components\Repositories\LocalFileReader;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class JavaScriptConfigurationParserTest extends TestCase {
	/**
	 * Test data provider for string versions
	 *
	 * @return array[]
	 */
	public function providerPath(): array {
		return [
			'JSON File' => [ __DIR__ . '/../Configurations/sample.json' ],
			'JS File'   => [ __DIR__ . '/../Configurations/sample.js' ],
		];
	}

	/**
	 * @dataProvider providerPath
	 *
	 * @param string $_filePath Source file path
	 */
	public function testCheckStringBuiltType( string $_filePath ): void {
		$localFile     = ( new LocalFileReader() )->read( $_filePath );
		$configuration = ( new JavaScriptConfigurationParser() )->read( $localFile );
		$this->assertIsArray( $configuration );
	}
}
