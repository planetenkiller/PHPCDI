<?php

namespace PHPCDI\API;

/**
 * Base class for all annotations
 */
abstract class Annotation extends \Doctrine\Common\Annotations\Annotation {
    public static function newInstance($values=array()) {
        return new static($values);
    }
    
    public static function className() {
        return get_called_class();
    }
}
