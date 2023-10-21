<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * String data stream model
 *
 * @package kanopi/components
 */
interface IStream {
	/**
	 * @return IStreamProperties
	 */
	public function properties(): IStreamProperties;

	/**
	 * @return string
	 */
	public function stream(): string;
}
