<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface AnnotatedType extends Annotated {
    /**
     * @return ReflectionClass
     */
    public function getPHPClass();

    /**
     * @return AnnotatedConstructor
     */
    public function getConstructor();

    /**
     * @return array of AnnotatedMethod
     */
    public function getMethods();

    /**
     * @return array of AnnotatedField
     */
    public function getFields();
}
