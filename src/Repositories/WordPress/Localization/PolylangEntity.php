<?php

namespace Kanopi\Components\Repositories\WordPress\Localization;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Localization\{TranslatableEntity, TranslationEntityIndexItem};
use Kanopi\Components\Repositories\Localization\LocalizationEntity;

/**
 * Polylang provider entity data interface
 *
 * @package kanopi/components
 */
class PolylangEntity implements LocalizationEntity {
	/**
	 * {@inheritDoc}
	 */
	public function readAssociatedEntities( IIndexedEntity $_entity ): EntityIterator {
		$languagePairs    = pll_get_post_translations( $_entity->indexIdentifier() );
		$languageEntities = [];

		foreach ($languagePairs as $code => $id) {
			$languageEntities[$code] = new TranslationEntityIndexItem( $id, $code );
		}

		return EntityIterator::fromArray( $languageEntities, TranslationEntityIndexItem::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function readEntityLanguage( int $_entityId ): string {
		return pll_get_post_language( $_entityId );
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateAssociatedEntities( EntityIterator $_entities ): void {
		$associations = [];

		/**
		 * @var IIndexedEntity&TranslatableEntity $entity
		 */
		foreach ($_entities as $code => $entity) {
			$associations[$code] = $entity->indexIdentifier();
		}

		pll_save_post_translations( $associations );
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateEntityLanguage( int $_entityId, string $_languageCode ): void {
		pll_set_post_language( $_entityId, $_languageCode );
	}
}
