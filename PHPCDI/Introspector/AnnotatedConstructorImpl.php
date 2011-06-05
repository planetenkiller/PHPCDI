<?php

namespace PHPCDI\Introspector;

use PHPCDI\SPI\AnnotatedConstructor;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;

class AnnotatedConstructorImpl extends AbstractAnnotatedMethod implements AnnotatedConstructor {

    public function __construct(AnnotatedType $class, \ReflectionMethod $method) {
        $returnType = AnnotationUtil::getReturnType($method);
        parent::__construct(AnnotationUtil::reader()->getMethodAnnotations($method),
                            !empty($returnType)? $returnType : 'mixed',
                            !empty($returnType)? 
                                ReflectionUtil::getClassNames(new \ReflectionClass($returnType))
                              :
                                array('mixed'),
                            $class,
                            $method);
    }

    public function isStatic() {
        return false;
    }

    public function __toString() {
        return 'Constructor of ' . $this->getPHPMember()->class;
    }
}
