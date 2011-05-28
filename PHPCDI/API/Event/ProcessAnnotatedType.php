<?php

namespace PHPCDI\API\Event;

interface ProcessAnnotatedType {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedType
     */
    public function getAnnotatedType();
    
    public function setAnnotatedType(\PHPCDI\API\Inject\SPI\AnnotatedType $type);
    
    public function veto();
}
