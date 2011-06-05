<?php

namespace PHPCDI\API\Event;

use PHPCDI\SPI\InjectionTarget;

interface ProcessInjectionTarget {
    /**
     * @return \PHPCDI\SPI\AnnotatedType
     */
    public function getAnnotatedType();
    
    /**
     * @return \PHPCDI\SPI\InjectionTarget
     */
    public function getInjectionTarget();
    
    
    public function setInjectionTarget(InjectionTarget $injectionTarget);
    
    
    public function addDefinitionError(\Exception $e);
}

