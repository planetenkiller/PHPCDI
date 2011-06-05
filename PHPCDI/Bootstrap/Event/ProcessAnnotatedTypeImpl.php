<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\API\Event\ProcessAnnotatedType;
use PHPCDI\SPI\AnnotatedType;

class ProcessAnnotatedTypeImpl implements ProcessAnnotatedType {
    private $type;
    private $veto = false;
    
    public function getAnnotatedType() {
        return $this->type;
    }
    
    public function setAnnotatedType(AnnotatedType $type) {
       $this->type = $type;
    }
    
    public function veto() {
        $this->veto = true;
    }
    
    public function hasVeto() {
        return $this->veto;
    }
}

