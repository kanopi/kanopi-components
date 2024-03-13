<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Services\External\IExternalStreamReader;

trait RecurrentProcessor {
	use CoreProcessor;

	/**
	 * External data import source service
	 *
	 * @return IExternalStreamReader
	 */
	abstract protected function externalService(): IExternalStreamReader;

	/**
	 * Read the set of external entities, converted to the common entity type, to process
	 *
	 * @return iterable
	 */
	protected function readExternalEntities(): iterable {
		$entities = $this->externalService()->read();
		$this->processStatistics()->incomingTotal( $entities->count() );
		return $entities;
	}
}
