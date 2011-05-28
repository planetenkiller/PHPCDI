<?php

namespace PHPCDI\Bootstrap\Event;

class ProcessAnnotatedTypeImpl implements \PHPCDI\API\Event\ProcessAnnotatedType {
    private $type;
    private $veto = false;
    
    public function getAnnotatedType() {
        return $this->type;
    }
    
    public function setAnnotatedType(\PHPCDI\API\Inject\SPI\AnnotatedType $type) {
       $this->type = $type;
    }
    
    public function veto() {
        $this->veto = tru;
    }
    
    public function hasVeto() {
        return $this->veto;
    }
}

