<?php

namespace PHPCDI\Introspector;

use PHPCDI\SPI\AnnotatedField;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;

class AnnotatedFieldImpl implements AnnotatedField {

    private $reflectionProperty;
    private $annotations;
    private $annotatedType;
    private $returnType;
    private $baseType;
    private $allTypes;

    public function __construct(AnnotatedType $type, \ReflectionProperty $property) {
        $this->reflectionProperty = $property;
        $this->annotations = AnnotationUtil::reader()->getPropertyAnnotations($property);
        $this->annotatedType = $type;
        $this->baseType = AnnotationUtil::getPropertyType($property);
        if(empty ($this->baseType)) {
            $this->baseType = 'mixed';
            $this->allTypes = array('mixed');
        } else {
            $this->baseType = ReflectionUtil::resolveRelativeClassName($this->baseType, $property->getDeclaringClass());
            $this->allTypes = ReflectionUtil::getClassNames($this->baseType);
        }
    }

    public function getAnnotation($annotationType) {
        return isset($this->annotations[$annotationType])? $this->annotations[$annotationType] : null;
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function isAnnotationPresent($className) {
        return isset($this->annotations[$className]) && $this->annotations[$className] != null;
    }

    public function getBaseType() {
        return $this->baseType;
    }

    public function getDeclaringType() {
        return $this->annotatedType;
    }

    public function getPHPMember() {
        return $this->reflectionProperty;
    }

    public function getTypeClosure() {
        return $this->allTypes;
    }

    public function isStatic() {
        return $this->reflectionProperty->isStatic();
    }
}
