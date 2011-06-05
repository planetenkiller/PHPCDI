<?php

namespace PHPCDI\SPI;

use PHPCDI\SPI\Context\Contextual;

interface Bean extends Contextual {
    public function getTypes();
    public function getQualifiers();
    /**
     * @return string class name
     */
    public function getScope();
    public function getName();
    public function getStereotypes();
    public function getBeanClass();
    public function isAlternative();
    public function isNullable();
    
    /**
     * @return InjectionPoint[]
     */
    public function getInjectionPoints();

}

