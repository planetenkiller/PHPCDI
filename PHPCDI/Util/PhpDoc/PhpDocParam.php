<?php

namespace PHPCDI\Util\PhpDoc;

/**
 * Phpdoc @param annotation
 */
class PhpDocParam extends \Doctrine\Common\Annotations\Annotation {

    /**
     * Return type of the method parameter
     * @var string
     */
    public $type;

    /**
     * Name of the method parameter
     * @var string
     */
    public $name;
}

