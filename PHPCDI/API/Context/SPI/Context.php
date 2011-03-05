<?php

namespace PHPCDI\API\Context\SPI;

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

    /**
     * @param Contextual $bean
     * @param CreationalContext $creationalContext
     */
    public function get($bean, $creationalContext=null);
}

