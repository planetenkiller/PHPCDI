<?php

namespace PHPCDI\Introspector;

class AnnotatedMethodImpl extends AbstractAnnotatedMethod implements \PHPCDI\API\Inject\SPI\AnnotatedMethod {

    public function __construct(\PHPCDI\API\Inject\SPI\AnnotatedType $class, \ReflectionMethod $method) {
        $returnType = \PHPCDI\Util\Annotations::getReturnType($method);
        parent::__construct(\PHPCDI\Util\Annotations::reader()->getMethodAnnotations($method),
                            !empty($returnType)? $returnType : 'mixed',
                            !empty($returnType)?
                                \PHPCDI\Util\ReflectionUtil::getClassNames($returnType)
                              :
                                array('mixed'),
                            $class,
                            $method);
    }

    public function isStatic() {
        return false;
    }
}
