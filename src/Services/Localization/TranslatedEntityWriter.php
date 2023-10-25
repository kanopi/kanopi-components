<?php

namespace Kanopi\Components\Services\Localization;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * Add the ability to associate multiple translations to each other
 *
 * @package kanopi/components
 */
interface TranslatedEntityWriter extends IIndexedEntityWriter {
	/**
	 * Update the association between multiple translated entities
	 *
	 * @param EntityIterator $_entities Set of associated entities
	 *
	 * @return void
	 */
	public function updateTranslationAssociations( EntityIterator $_entities ): void;
}
