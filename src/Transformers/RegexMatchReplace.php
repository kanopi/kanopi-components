<?php

namespace Kanopi\Components\Transformers;

use Kanopi\Components\Model\Transform\RegexMatch;

class RegexMatchReplace {
	/**
	 * @var string
	 */
	protected string $_exclude;
	/**
	 * @var string
	 */
	protected string $_replace;
	/**
	 * @var string
	 */
	protected string $_search;

	public function __construct(
		string $_search,
		string $_replace,
		string $_exclude = ''
	) {
		$this->_search  = $_search;
		$this->_replace = $_replace;
		$this->_exclude = $_exclude;
	}

	/**
	 * Tests the $_original subject for a match or exclusion.
	 *
	 * @param string $_original
	 *
	 * @return RegexMatch
	 */
	public function replace( string $_original ): RegexMatch {
		$replacement              = new RegexMatch();
		$replacement->original    = $_original;
		$replacement->is_excluded = !empty( $this->_exclude ) && 1 === preg_match( $this->_exclude, $_original );
		$replacement->replacement = preg_replace( $this->_search, $this->_replace, $_original, -1, $matches );
		$replacement->is_match    = 0 < $matches;
		return $replacement;
	}
}
