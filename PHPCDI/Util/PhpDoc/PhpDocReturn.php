<?php

namespace PHPCDI\Util\PhpDoc;

/**
 * Phpdoc return annotation
 */
class PhpDocReturn extends \Doctrine\Common\Annotations\Annotation {

    /**
     * Return type of the method
     * @var string
     */
    public $type;
}

