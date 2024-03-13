<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Cursor pagination
 *
 * @package kanopi/components
 */
interface CursorPagination {
	/**
	 * Current offset GUID/path
	 *
	 * @return string
	 */
	public function currentOffset(): string;

	/**
	 * The effective maximum batch size, at a minimum equal to the effective page size
	 *
	 * @return int
	 */
	public function effectiveMaxSize(): int;

	/**
	 * The effective page size, clamped between 1 max for the external source
	 *
	 * @return int
	 */
	public function effectivePageSize(): int;

	/**
	 * Status check, whether to use an offset
	 *
	 * @return bool
	 */
	public function useOffset(): bool;

	/**
	 * Status check, whether to use Pagination
	 *
	 * @return bool
	 */
	public function usePagination(): bool;
}
