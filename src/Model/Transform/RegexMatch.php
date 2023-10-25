<?php

namespace Kanopi\Components\Model\Transform;

/**
 * Structure for the results of a single Regex Match
 *
 * @package kanopi/components
 */
class RegexMatch {
	/**
	 * @var boolean
	 */
	public bool $is_excluded;
	/**
	 * @var boolean
	 */
	public bool $is_match;
	/**
	 * @var string
	 */
	public string $original;
	/**
	 * @var string|null
	 */
	public ?string $replacement;
}
