<?php

namespace PHPCDI\Bootstrap\Event;

class ProcessBeanImpl implements \PHPCDI\API\Event\ProcessBean{
    private $erros = array();
    private $bean;
    private $type;


    public function __construct(\PHPCDI\API\Inject\SPI\Bean $bean, \PHPCDI\API\Inject\SPI\AnnotatedType $type=null) {
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

