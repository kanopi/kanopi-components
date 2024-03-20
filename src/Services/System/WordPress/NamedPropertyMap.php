<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\IPostTypeEntity;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Transformers\Arrays;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use WP_Post;

/**
 * Post Metadata reader
 *  - Relies on WP post meta key names matching the implementing classes property names
 *  - If that is not possible (uses dashes), implement this in a different manner (w/o this trait)
 *  - Register mapping additional meta property types through registerTypeAction
 *
 * @package kanopi/components
 */
trait NamedPropertyMap {
	/**
	 * Set of callable functions/actions, keyed by built-in type or fully qualified class name
	 *
	 * @var Arrays|null
	 */
	protected ?Arrays $typeActions = null;

	/**
	 * Action provides access to the current entity prior to mapping its property fields
	 *  - Override for additional processing, default is a pass-through
	 *
	 * @param IPostTypeEntity $_entity Current Entity
	 *
	 * @return IPostTypeEntity
	 */
	protected function beforeEntityMapping( IPostTypeEntity $_entity ): IPostTypeEntity {
		return $_entity;
	}

	/**
	 * Initialize the set of standard actions for built-in PHP variable types
	 *  - Only assigns the defaults if the underlying typeActions is null,
	 *      circumvents lack of built-in constructor access
	 *
	 * @return void
	 */
	protected function initializeTypeActions(): void {
		if ( null === $this->typeActions ) {
			$this->typeActions = Arrays::from(
				[
					'string' => function ( IPostTypeEntity $_entity, string $_metaKey, mixed $_metaValue ) {
						$_entity->{$_metaKey} = $_metaValue;
					},
					'float'  => function ( IPostTypeEntity $_entity, string $_metaKey, mixed $_metaValue ) {
						$_entity->{$_metaKey} = floatval( $_metaValue );
					},
					'int'    => function ( IPostTypeEntity $_entity, string $_metaKey, mixed $_metaValue ) {
						$_entity->{$_metaKey} = intval( $_metaValue );
					},
					'bool'   => function ( IPostTypeEntity $_entity, string $_metaKey, mixed $_metaValue ) {
						$_entity->{$_metaKey} = '1' === $_metaValue;
					},
				]
			);
		}
	}

	/**
	 * Register/overwrite callable/function action for given _typeName
	 *  - Action is of form function ( IPostTypeEntity $_entity, string $_metaKey, string $_metaValue ): void { ... }
	 *
	 * @param string   $_typeName Built-in type or fully qualified class name
	 * @param callable $_action   Action to run
	 * @return void
	 */
	protected function registerTypeAction( string $_typeName, callable $_action ): void {
		$this->initializeTypeActions();
		$this->typeActions->writeIndex( $_typeName, $_action );
	}

	/**
	 * Run an action to set the value for a Property based on type, entity, and meta
	 *  - Does nothing if the Property type has no assigned action
	 *
	 * @param string          $_type      Entity property type
	 * @param IPostTypeEntity $_entity    Entity
	 * @param string          $_metaKey   Meta key
	 * @param mixed           $_metaValue Meta value
	 *
	 * @return IPostTypeEntity Modified entity
	 */
	protected function runPropertyTypeAction(
		string $_type,
		IPostTypeEntity $_entity,
		string $_metaKey,
		mixed $_metaValue
	): IPostTypeEntity {
		if ( $this->typeActions->containsKey( $_type ) ) {
			call_user_func( $this->typeActions->readIndex( $_type ), $_entity, $_metaKey, $_metaValue );
		}

		return $_entity;
	}

	/**
	 * @return ISetReader
	 */
	abstract protected function metaDataRepository(): ISetReader;

	/**
	 * Reads the class name to find all class properties
	 *  - Expected to return Entity\Class::class or equivalent
	 *  - Target class must implement Kanopi\Components\Model\Data\WordPress\IPostTypeEntity
	 *
	 * @see IPostTypeEntity
	 *
	 * @return string
	 */
	abstract protected function readEntityClassName(): string;

	/**
	 * @param string $_field Field name
	 *
	 * @return ReflectionNamedType|null
	 */
	public function readPropertyType( string $_field ): ?ReflectionNamedType {
		try {
			return ( new ReflectionProperty( $this->readEntityClassName(), $_field ) )->getType();
		} catch ( ReflectionException $exception ) {
			return null;
		}
	}

	/**
	 * Read a system entity from the WP_Post entity and all associated metadata
	 *
	 * @param WP_Post $_postEntity WP_Post entity
	 *
	 * @return IPostTypeEntity|null
	 * @throws SetReaderException Unable to read post meta-data
	 */
	public function readSystemEntity( WP_Post $_postEntity ): ?IPostTypeEntity {
		/**
		 * Guard to keep avoid errors when the provided class name is incorrect
		 * and fails to implement the IPostTypeEntity interface
		 */
		$className  = $this->readEntityClassName();
		$interfaces = class_implements( $className );
		if ( ! $interfaces || ! in_array( IPostTypeEntity::class, $interfaces, true ) ) {
			return null;
		}

		// Ensure the default actions are registered
		$this->initializeTypeActions();

		// Read any existing instance of the entity core attributes and meta-data
		$entity   = $this->beforeEntityMapping( $className::fromWPPost( $_postEntity ) );
		$metaData = $this->metaDataRepository()->read( $entity->indexIdentifier() );

		/**
		 * Read the full set of metadata, indexed by meta key
		 * All meta values are strings, add default to empty strings if not found
		 */
		foreach ( Arrays::from( $entity->metaFieldMapping() )->keys() as $metaKey ) {
			$type = $this->readPropertyType( $metaKey );
			if ( null === $type ) {
				continue;
			}

			$metaValue = $metaData->offsetExists( $metaKey ) ? ( $metaData->offsetGet( $metaKey )[0] ?? '' ) : '';
			$entity    = $this->runPropertyTypeAction( $type->getName(), $entity, $metaKey, $metaValue );
		}

		return $entity;
	}
}
