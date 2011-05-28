<?php

namespace PHPCDI\Introspector;

class AnnotatedFieldImpl implements \PHPCDI\API\Inject\SPI\AnnotatedField {

    private $reflectionProperty;
    private $annotations;
    private $annotatedType;
    private $returnType;
    private $baseType;
    private $allTypes;

    public function __construct(\PHPCDI\API\Inject\SPI\AnnotatedType $type, \ReflectionProperty $property) {
        $this->reflectionProperty = $property;
        $this->annotations = \PHPCDI\Util\Annotations::reader()->getPropertyAnnotations($property);
        $this->annotatedType = $type;
        $this->baseType = \PHPCDI\Util\Annotations::getPropertyType($property);
        if(empty ($this->baseType)) {
            $this->baseType = 'mixed';
            $this->allTypes = array('mixed');
        } else {
            $this->baseType = \PHPCDI\Util\ReflectionUtil::resolveRelativeClassName($this->baseType, $property->getDeclaringClass());
            $this->allTypes = \PHPCDI\Util\ReflectionUtil::getClassNames($this->baseType);
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
