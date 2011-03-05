<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface Bean extends \PHPCDI\API\Context\SPI\Contextual {
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
    public function getInjectionPoints();

}

