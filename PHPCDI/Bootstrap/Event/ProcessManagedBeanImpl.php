<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessManagedBean;
use PHPCDI\Bean\ManagedBean;

class ProcessManagedBeanImpl implements ProcessManagedBean {
    private $erros = array();
    private $bean;
    
    public function __construct(ManagedBean $bean) {
        $this->bean = $bean;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }

    public function getAnnotated() {
        return $this->bean->getAnnotatedType();
    }

    public function getAnnotatedBeanClass() {
        return $this->bean->getAnnotatedType();
    }

    public function getBean() {
        return $this->bean;
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

