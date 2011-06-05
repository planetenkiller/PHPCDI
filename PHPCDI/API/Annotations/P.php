<?php

namespace PHPCDI\API\Annotations;

use PHPCDI\API\Annotation;

/**
 * Annotation to specify parameter specify annotations
 */
class P extends Annotation {
    /**
     * Name of the paramter
     * @var String
     */
    public $name;

    /**
     * Type of the parameter (optional)
     * @var string
     */
    public $type;
}

