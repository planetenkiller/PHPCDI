<?php

namespace PHPCDI\API\Inject;

/**
 * Annotation to specify parameter specify annotations
 *
 *
 */
class P extends \Doctrine\Common\Annotations\Annotation {
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

