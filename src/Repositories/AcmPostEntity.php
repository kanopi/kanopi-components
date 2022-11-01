<?php
/**
 * Provide writable CRUD methods for ACM drive post entities
 *
 * @package kanopi/utilities
 */

namespace Kanopi\Utilities\Repositories;

use Kanopi\Utilities\Model\Data\IAcmIndexedEntity;
use Kanopi\Utilities\Model\Exception\AcmWriterException;
use WP_Error;

use function WPE\AtlasContentModeler\API\insert_model_entry;
use function WPE\AtlasContentModeler\API\update_model_entry;

class AcmPostEntity extends PostQuery implements IAcmSetWriter {
	/**
	 * @inheritDoc
	 */
	public function create( IAcmIndexedEntity $_entity ): int {
		$result = insert_model_entry(
			$_entity->entityName(),
			$_entity->metaData(),
			$_entity->coreData()
		);

		if ( is_a( $result, WP_Error::class ) ) {
			throw new AcmWriterException(
				"Cannot create ACM entity: " . join( ';', $result->get_error_messages() ) );
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function delete( int $_index_identifier ): void {
		wp_delete_post( $_index_identifier, true );
	}

	/**
	 * @inheritDoc
	 */
	public function update( IAcmIndexedEntity $_entity ): bool {
		$result = update_model_entry(
			$_entity->indexIdentifier(),
			$_entity->metaData(),
			$_entity->coreData()
		);

		if ( is_a( $result, WP_Error::class ) ) {
			throw new AcmWriterException(
				"Cannot update ACM entity: " . join( ';', $result->get_error_messages() ) );
		}

		return $result;
	}
}