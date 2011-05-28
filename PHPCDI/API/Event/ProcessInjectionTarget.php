<?php

namespace PHPCDI\API\Event;

interface ProcessInjectionTarget {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedType
     */
    public function getAnnotatedType();
    
    /**
     * @return \PHPCDI\API\Inject\SPI\InjectionTarget
     */
    public function getInjectionTarget();
    
    
    public function setInjectionTarget(\PHPCDI\API\Inject\SPI\InjectionTarget $injectionTarget);
    
    
    public function addDefinitionError(\Exception $e);
}

