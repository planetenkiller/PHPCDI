<?php

namespace PHPCDI\API\Inject;

/**
 * NormalScope Annotation.
 */
class NormalScope extends \Doctrine\Common\Annotations\Annotation {
    /**
     * @var boolean
     */
    public $passivating = false;
}

