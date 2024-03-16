<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream cursor pagination
 *
 * @package kanopi/components
 */
class StreamCursorPagination implements CursorPagination {
	/**
	 * Maximum records to read
	 *
	 * @var int
	 */
	public int $maxSize = 10000;
	/**
	 * Request offset record
	 *
	 * @var string
	 */
	public string $offset = '';
	/**
	 * Records per page
	 *
	 * @var int
	 */
	public int $pageSize = 500;

	/**
	 * @var int
	 */
	private int $maximumAllowedPageSize;

	/**
	 * Build a pagination model
	 *  - Allows overriding the default maximum allowed page size, used to clamp effective page size
	 *
	 * @param int $_maximumAllowedPageSize Maximum allowed page size (default 100)
	 */
	public function __construct(
		int $_maximumAllowedPageSize = 100
	) {
		$this->maximumAllowedPageSize = $_maximumAllowedPageSize;
	}

	/**
	 * Current offset GUID/path
	 *
	 * @return string
	 */
	public function currentOffset(): string {
		return $this->offset;
	}

	/**
	 * The effective maximum batch size, at a minimum equal to the effective page size
	 *
	 * @return int
	 */
	public function effectiveMaxSize(): int {
		return max( $this->maxSize, $this->effectivePageSize() );
	}

	/**
	 * The effective page size, clamped between 1 and maximumAllowedPageSize()
	 *
	 * @return int
	 */
	public function effectivePageSize(): int {
		return max( 1, min( $this->maximumAllowedPageSize, $this->pageSize ) );
	}

	/**
	 * Status check, whether to use an offset
	 *
	 * @return bool
	 */
	public function useOffset(): bool {
		return ! empty( $this->offset );
	}

	/**
	 * Status check, whether to use Pagination
	 *
	 * @return bool
	 */
	public function usePagination(): bool {
		return $this->effectiveMaxSize() > $this->effectivePageSize();
	}
}
