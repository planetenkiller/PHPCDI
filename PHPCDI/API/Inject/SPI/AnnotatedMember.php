<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface AnnotatedMember extends Annotated {
    /**
     * @return ReflectionMethod
     */
    public function getPHPMember();

    public function isStatic();

    /**
     * @return AnnotatedType
     */
    public function getDeclaringType();
}
