<?php

namespace PHPCDI\Bootstrap\Event;

class ProcessProducerImpl implements \PHPCDI\API\Event\ProcessProducer {
    private $erros = array();
    private $bean;
    
    public function __construct(\PHPCDI\Bean\AbstractProducer $bean) {
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

    public function setProducer(\PHPCDI\API\Inject\SPI\Producer $producer) {
        $this->bean->setProducer($producer);
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

