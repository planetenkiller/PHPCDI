<?php

namespace PHPCDI\SPI\Context;

use PHPCDI\SPI\Context\Contextual;
use PHPCDI\SPI\Context\CreationalContext;

/**
 *
 */
interface Context {
    /**
     * @return string scope annotation class name of this context
     */
    public function getScope();

    /**
     * @return boolean
     */
    public function isActive();

    public function get(Contextual $bean, CreationalContext $creationalContext=null);
}

