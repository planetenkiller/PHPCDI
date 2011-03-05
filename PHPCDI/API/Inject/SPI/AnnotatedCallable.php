<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface AnnotatedCallable extends AnnotatedMember {
    /**
     * @return array of AnnotatedParameter
     */
    public function getParameters();
}
