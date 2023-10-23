<?php

namespace Kanopi\Components\Transformers;

use Kanopi\Components\Model\Transform\RegexMatch;

/**
 * Transform Regex replacements with structured original and result references
 *
 * @package kanopi/components
 */
class RegexMatchReplace {
	/**
	 * @var string
	 */
	protected string $exclude;
	/**
	 * @var string
	 */
	protected string $replace;
	/**
	 * @var string
	 */
	protected string $search;

	/**
	 * Replacement structure construction
	 *
	 * @param string $_search  RegEx search pattern
	 * @param string $_replace Match replacement
	 * @param string $_exclude Exclusion string, sets flag if found in original subject
	 */
	public function __construct(
		string $_search,
		string $_replace,
		string $_exclude = ''
	) {
		$this->search  = $_search;
		$this->replace = $_replace;
		$this->exclude = $_exclude;
	}

	/**
	 * Tests the $_original subject for a match or exclusion.
	 *
	 * @param string $_original Original string
	 *
	 * @return RegexMatch
	 */
	public function replace( string $_original ): RegexMatch {
		$replacement              = new RegexMatch();
		$replacement->original    = $_original;
		$replacement->is_excluded = ! empty( $this->exclude ) && 1 === preg_match( $this->exclude, $_original );
		$replacement->replacement = preg_replace( $this->search, $this->replace, $_original, -1, $matches );
		$replacement->is_match    = 0 < $matches;
		return $replacement;
	}
}
