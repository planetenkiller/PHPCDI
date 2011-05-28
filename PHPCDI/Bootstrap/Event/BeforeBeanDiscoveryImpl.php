<?php

namespace PHPCDI\Bootstrap\Event;

class BeforeBeanDiscoveryImpl implements \PHPCDI\API\Event\BeforeBeanDiscovery {
    
    private $types = array();
    
    public function addAnnotatedType(\PHPCDI\API\Inject\SPI\AnnotatedType $annotatedType) {
        $this->types[] = $annotatedType;
    }
    
    public function getTypes() {
        return $this->types;
    }
}

