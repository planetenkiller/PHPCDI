<?php

namespace PHPCDI\SPI;

/**
 *
 */
interface AnnotatedType extends Annotated {
    /**
     * @return \ReflectionClass
     */
    public function getPHPClass();

    /**
     * @return AnnotatedConstructor
     */
    public function getConstructor();

    /**
     * @return AnnotatedMethod[] methods
     */
    public function getMethods();

    /**
     * @return AnnotatedField[] fields
     */
    public function getFields();
}
