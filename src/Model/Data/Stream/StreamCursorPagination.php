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
		return min( $this->maxSize, $this->effectivePageSize() );
	}

	/**
	 * The effective page size, clamped between 1 and 100 (max for API)
	 *
	 * @return int
	 */
	public function effectivePageSize(): int {
		return max( 1, min( 100, $this->pageSize ) );
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
