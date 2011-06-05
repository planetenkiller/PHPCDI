<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessProducerMethod;
use PHPCDI\Bean\ProducerMethod;

class ProcessProducerMethodImpl implements ProcessProducerMethod {
    private $erros = array();
    private $bean;
    
    public function __construct(ProducerMethod $bean) {
        $this->bean = $bean;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }

    public function getAnnotated() {
        return $this->bean->getMember();
    }

    public function getAnnotatedDisposedParameter() {
        $method = $this->bean->getDisposer();
        $params = $method->getParameters();
        
        return $params[0];
    }

    public function getAnnotatedProducerMethod() {
        return $this->bean->getMember();
    }

    public function getBean() {
        return $this->bean;
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

