<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessProducerField;
use PHPCDI\Bean\ProducerField;

class ProcessProducerFieldImpl implements ProcessProducerField {
    private $erros = array();
    private $bean;
    
    public function __construct(ProducerField $bean) {
        $this->bean = $bean;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }

    public function getAnnotated() {
        return $this->bean->getMember();
    }

    public function getAnnotatedProducerField() {
        return $this->bean->getMember();
    }

    public function getBean() {
        return $this->bean;
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

