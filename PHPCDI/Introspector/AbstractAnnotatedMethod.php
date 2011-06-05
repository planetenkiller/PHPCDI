<?php

namespace PHPCDI\Introspector;

use PHPCDI\SPI\AnnotatedCallable;
use PHPCDI\SPI\AnnotatedType;

abstract class AbstractAnnotatedMethod implements AnnotatedCallable {

    private $annotations;
    private $baseType;
    private $allTypes;
    private $class;
    private $reflectionMethod;
    private $parameters = null;

    public function __construct($annotations, $baseType, $allTypes, AnnotatedType $class, \ReflectionMethod $method) {
        $this->annotations = $annotations;
        $this->baseType = $baseType;
        $this->allTypes = $allTypes;
        $this->class = $class;
        $this->reflectionMethod = $method;
    }
    
    private function initParameters() {
        $this->parameters = array();
        foreach($this->reflectionMethod->getParameters() as $parameter) {
            $this->parameters[] = new AnnotatedParameterImpl($this, $parameter);
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
        return $this->class;
    }

    public function getPHPMember() {
        return $this->reflectionMethod;
    }

    public function getParameters() {
        if($this->parameters == null) {
            $this->initParameters();
        }
        
        return $this->parameters;
    }

    public function getTypeClosure() {
        return $this->allTypes;
    }
}

