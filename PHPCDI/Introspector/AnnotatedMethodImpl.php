<?php

namespace PHPCDI\Introspector;

use PHPCDI\SPI\AnnotatedMethod;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;

class AnnotatedMethodImpl extends AbstractAnnotatedMethod implements AnnotatedMethod {

    public function __construct(AnnotatedType $class, \ReflectionMethod $method) {
        $returnType = AnnotationUtil::getReturnType($method);
        parent::__construct(AnnotationUtil::reader()->getMethodAnnotations($method),
                            !empty($returnType)? $returnType : 'mixed',
                            !empty($returnType)?
                                ReflectionUtil::getClassNames($returnType)
                              :
                                array('mixed'),
                            $class,
                            $method);
    }

    public function isStatic() {
        return false;
    }
}
