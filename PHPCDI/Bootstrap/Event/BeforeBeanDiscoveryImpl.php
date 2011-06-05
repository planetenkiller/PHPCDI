<?php

namespace PHPCDI\Bootstrap\Event;

use PHPCDI\SPI\AnnotatedType;
use PHPCDI\API\Event\BeforeBeanDiscovery;

class BeforeBeanDiscoveryImpl implements BeforeBeanDiscovery {
    
    private $types = array();
    
    public function addAnnotatedType(AnnotatedType $annotatedType) {
        $this->types[] = $annotatedType;
    }
    
    public function getTypes() {
        return $this->types;
    }
}

