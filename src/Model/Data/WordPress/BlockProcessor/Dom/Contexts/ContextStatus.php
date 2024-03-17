<?php

namespace Kanopi\Components\Model\Data\WordPress\BlockProcessor\Dom\Contexts;

/**
 * Transform context status tracking data structure
 *
 * @package kanopi/components
 */
class ContextStatus {
	/**
	 * @var bool
	 */
	protected bool $ended = false;

	/**
	 * Whether the currently processed node was transformed by the context
	 *  - Yes (true) when the current node ended the Context AND was used/transformed
	 *  - No (false), use when the current node indicated the end but is not in the context
	 *
	 * @var bool
	 */
	protected bool $currentNodeUsed = false;

	/**
	 * @var bool
	 */
	protected bool $started = false;

	/**
	 * Mark context as ended
	 *
	 * @return ContextStatus
	 */
	public function end(): ContextStatus {
		$this->ended = true;

		return $this;
	}

	/**
	 * Flag if the process is started
	 *
	 * @return bool
	 */
	public function isCurrentNodeUsed(): bool {
		return $this->currentNodeUsed;
	}

	/**
	 * Flag if the process is started
	 *
	 * @return bool
	 */
	public function isEnded(): bool {
		return $this->ended;
	}

	/**
	 * Flag if the process is started
	 *
	 * @return bool
	 */
	public function isStarted(): bool {
		return $this->started;
	}

	/**
	 * Mark context as started
	 *
	 * @return ContextStatus
	 */
	public function start(): ContextStatus {
		$this->started = true;

		return $this;
	}

	/**
	 * Mark the current node used
	 *
	 * @return ContextStatus
	 */
	public function useNode(): ContextStatus {
		$this->currentNodeUsed = true;

		return $this;
	}

	/**
	 * Fluent method to indicate a Context Status of No Context
	 *
	 * @return ContextStatus
	 */
	public static function none(): ContextStatus {
		return ( new ContextStatus() );
	}

	/**
	 * Fluent method to get a started Context Status
	 *
	 * @return ContextStatus
	 */
	public static function started(): ContextStatus {
		return ( new ContextStatus() )->start();
	}
}
