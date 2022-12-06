<?php

namespace Kanopi\Components\Model\Data;

interface IStream {
	/**
	 * @return IStreamProperties
	 */
	function properties(): IStreamProperties;

	/**
	 * @return string
	 */
	function stream(): string;
}