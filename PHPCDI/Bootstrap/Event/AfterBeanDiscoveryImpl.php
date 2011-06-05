<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\SPI\Bean;
use PHPCDI\SPI\Context\Context;
use PHPCDI\SPI\ObserverMethod;
use PHPCDI\API\Event\AfterBeanDiscovery;

class AfterBeanDiscoveryImpl implements AfterBeanDiscovery {
    private $beans = array();
    private $contexts = array();
    private $errors = array();
    private $observers = array();

    public function addBean(Bean $bean) {
        $this->beans[] = $bean;
    }

    public function addContext(Context $context) {
        $this->contexts[] = $context;
    }

    public function addDefinitionError(\Exception $e) {
        $this->errors[] = $e;
    }

    public function addObserverMethod(ObserverMethod $observerMethod) {
        $this->observers[] = $observerMethod;
    }
    
    public function getBeans() {
        return $this->beans;
    }
    
    public function getContexts() {
        return $this->contexts;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getObservers() {
        return $this->observers;
    }
}

