<?php
namespace Kanopi\Components\Models\Transform;

class RegexMatch {
	/**
	 * @var boolean
	 */
	public $is_excluded;

	/**
	 * @var boolean
	 */
	public $is_match;

	/**
	 * @var string
	 */
	public $original;

	/**
	 * @var string|null
	 */
	public $replacement;
}
