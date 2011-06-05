<?php

namespace PHPCDI\SPI;

/**
 *
 */
interface AnnotatedParameter extends Annotated {
    public function getName();
    public function getPosition();
    
    /**
     * @return AnnotatedCallable
     */
    public function getDeclaringCallable();
}
