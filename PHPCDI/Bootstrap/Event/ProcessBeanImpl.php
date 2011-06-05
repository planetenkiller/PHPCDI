<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessBean;
use PHPCDI\SPI\Bean;
use PHPCDI\SPI\AnnotatedType;

class ProcessBeanImpl implements ProcessBean {
    private $erros = array();
    private $bean;
    private $type;


    public function __construct(Bean $bean, AnnotatedType $type=null) {
        $this->bean = $bean;
        $this->type = $type;
    }
    
    public function addDefinitionError(\Exception $e) {
        $this->erros[] = $e;
    }

    public function getAnnotated() {
        return $this->type;
    }

    public function getBean() {
        return $this->bean;
    }
    
    public function getErrors() {
        return $this->erros;
    }
}

