<?php

namespace PHPCDI\Resolution;

/**
 * Finds the requested bean in a list of beans.
 */
interface Resolver {
    /**
     * @param string $beanType class name or primitive type
     * @param array $qualifiers array of annotation objects or class names
     *
     * @return \PHPCDI\API\Inject\SPI\Bean a bean that fits the requirements
     */
    public function reslove($beanType, $qualifiers);
}
