<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessProducer;
use PHPCDI\Bean\AbstractProducer;
use PHPCDI\SPI\Producer;

class ProcessProducerImpl implements ProcessProducer {
    private $erros = array();
    private $bean;
    
    public function __construct(AbstractProducer $bean) {
        $this->bean = $bean;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }

    public function getAnnotatedMember() {
        return $this->bean->getMember();
    }

    public function getProducer() {
        return $this->bean->getProducer();
    }

    public function setProducer(Producer $producer) {
        $this->bean->setProducer($producer);
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

