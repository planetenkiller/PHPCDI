<?php

namespace PHPCDI\Bootstrap\Event;

class AfterBeanDiscoveryImpl implements \PHPCDI\API\Event\AfterBeanDiscovery {
    private $beans = array();
    private $contexts = array();
    private $errors = array();
    private $observers = array();

    public function addBean(\PHPCDI\API\Inject\SPI\Bean $bean) {
        $this->beans[] = $bean;
    }

    public function addContext(\PHPCDI\API\Context\SPI\Context $context) {
        $this->contexts[] = $context;
    }

    public function addDefinitionError(\Exception $e) {
        $this->errors[] = $e;
    }

    public function addObserverMethod(\PHPCDI\API\Inject\SPI\ObserverMethod $observerMethod) {
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

