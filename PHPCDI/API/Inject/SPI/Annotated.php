<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface Annotated {
    /**
     * @return string class name
     */
    public function getBaseType();
    public function getTypeClosure();
    public function getAnnotation($annotationType);
    public function getAnnotations();

    /**
     * @param string $isAnnotationPresent class name
     */
    public function isAnnotationPresent($className);
}
