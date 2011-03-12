<?php

namespace PHPCDI\Introspector;

class AnnotatedConstructorImpl extends AbstractAnnotatedMethod implements \PHPCDI\API\Inject\SPI\AnnotatedConstructor {

    public function __construct(\PHPCDI\API\Inject\SPI\AnnotatedType $class, \ReflectionMethod $method) {
        $returnType = \PHPCDI\Util\Annotations::getReturnType($method);
        parent::__construct(\PHPCDI\Util\Annotations::reader()->getMethodAnnotations($method),
                            !empty($returnType)? $returnType : 'mixed',
                            !empty($returnType)? 
                                \PHPCDI\Util\ReflectionUtil::getClassNames(new \ReflectionClass($returnType))
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
