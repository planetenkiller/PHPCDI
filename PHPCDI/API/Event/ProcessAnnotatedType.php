<?php

namespace PHPCDI\API\Event;

use PHPCDI\SPI\AnnotatedType;

interface ProcessAnnotatedType {
    /**
     * @return \PHPCDI\SPI\AnnotatedType
     */
    public function getAnnotatedType();
    
    public function setAnnotatedType(AnnotatedType $type);
    
    public function veto();
}
