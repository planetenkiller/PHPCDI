<?php

namespace PHPCDI\SPI;

/**
 *
 */
interface AnnotatedMember extends Annotated {
    /**
     * @return \ReflectionMethod
     */
    public function getPHPMember();

    public function isStatic();

    /**
     * @return AnnotatedType
     */
    public function getDeclaringType();
}
