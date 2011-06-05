<?php

namespace PHPCDI\SPI;

/**
 *
 */
interface AnnotatedCallable extends AnnotatedMember {
    /**
     * @return AnnotatedParameter[] parameters
     */
    public function getParameters();
}
