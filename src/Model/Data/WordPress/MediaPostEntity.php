<?php

namespace Kanopi\Components\Model\Data\WordPress;

/**
 * External Media Post Entity
 *
 * @package kanopi/components
 */
interface MediaPostEntity extends IPostTypeEntity {
	/**
	 * Optional caption associated with the Media entity
	 */
	public function caption(): ?string;

	/**
	 * External/legacy source URL of the media entity before import
	 */
	public function externalUrl(): string;

	/**
	 * Filename for the entity
	 */
	public function fileName(): string;

	/**
	 * Legacy/external source Identifier of the media entity before import
	 *  - For instance a previous WordPress attachment post ID
	 */
	public function legacyId(): ?string;

	/**
	 * Read a meta field value of the media attachment by key
	 *
	 * @param string $_key Meta key name
	 *
	 * @return mixed Is null if not entry exist by the requested key
	 */
	public function readMeta( string $_key ): mixed;

	/**
	 * System URL friendly short name
	 */
	public function slug(): ?string;

	/**
	 * Write/overwrite an additional meta field to the media attachment
	 *
	 * @param string $_key Meta key name
	 * @param mixed  $_value Meta value name
	 */
	public function writeMeta( string $_key, mixed $_value ): MediaPostEntity;
}
