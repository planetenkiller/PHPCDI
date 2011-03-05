<?php

namespace PHPCDI\Util\PhpDoc;

/**
 * Phpdoc @var annotation
 */
class PhpDocVar extends \Doctrine\Common\Annotations\Annotation {

    /**
     * Type of an class attribute
     * @var string
     */
    public $type;
}

