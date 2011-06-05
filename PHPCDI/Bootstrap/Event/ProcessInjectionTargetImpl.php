<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessInjectionTarget;
use PHPCDI\SPI\InjectionTarget;

class ProcessInjectionTargetImpl implements ProcessInjectionTarget {
    private $erros = array();
    private $bean;
    
    public function __construct(\PHPCDI\Bean\ManagedBean $bean) {
        $this->bean = $bean;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }
    
    public function getAnnotatedType() {
        return $this->bean->getAnnotatedType();
    }
    
    public function getInjectionTarget() {
        return $this->bean->getInjectionTarget();
    }
    
    public function setInjectionTarget(InjectionTarget $injectionTarget) {
        $this->bean->setInjectionTarget($injectionTarget);
    }
    
    public function getErrors() {
        return $this->erros;
    }
}
