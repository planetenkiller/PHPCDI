<?php

namespace PHPCDI\API\Event;

interface AfterBeanDiscovery {
    public function addDefinitionError(\Exception $e);
    public function addBean(\PHPCDI\API\Inject\SPI\Bean $bean);
    public function addObserverMethod(\PHPCDI\API\Inject\SPI\ObserverMethod $observerMethod);
    public function addContext(\PHPCDI\API\Context\SPI\Context $context);
}

