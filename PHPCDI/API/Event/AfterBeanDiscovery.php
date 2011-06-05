<?php

namespace PHPCDI\API\Event;

use PHPCDI\SPI\Bean;
use PHPCDI\SPI\ObserverMethod;
use PHPCDI\SPI\Context\Context;

interface AfterBeanDiscovery {
    public function addDefinitionError(\Exception $e);
    public function addBean(Bean $bean);
    public function addObserverMethod(ObserverMethod $observerMethod);
    public function addContext(Context $context);
}

