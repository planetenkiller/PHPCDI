<?php

namespace PHPCDI\Bootstrap\Event;

class ProcessInjectionTargetImpl implements \PHPCDI\API\Event\ProcessInjectionTarget {
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
    
    public function setInjectionTarget(\PHPCDI\API\Inject\SPI\InjectionTarget $injectionTarget) {
        $this->bean->setInjectionTarget($injectionTarget);
    }
    
    public function getErrors() {
        return $this->erros;
    }
}
