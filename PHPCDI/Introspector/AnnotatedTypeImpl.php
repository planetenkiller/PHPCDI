<?php

namespace PHPCDI\Introspector;

use PHPCDI\SPI\AnnotatedType;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;

class AnnotatedTypeImpl implements AnnotatedType {

    private $className;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;
    private $annotations;
    private $constructor;
    private $fields = null;
    private $methods = null;

    public function __construct($className) {
        $this->className = $className;
        $this->reflectionClass = new \ReflectionClass($className);

        $this->annotations = AnnotationUtil::reader()->getClassAnnotations($this->reflectionClass);

        if($this->reflectionClass->getConstructor() != null) {
            $this->constructor = new AnnotatedConstructorImpl($this, $this->reflectionClass->getConstructor());
        }
    }
    
    private function initFields() {
        $this->fields = array();
        foreach(ReflectionUtil::getAllFields($this->reflectionClass) as $field) {
            $this->fields[] = new AnnotatedFieldImpl($this, $field);
        }
    }
    
    private function initMethods() {
        $this->methods = array();
        foreach(ReflectionUtil::getAllMethods($this->reflectionClass) as $method) {
            $this->methods[] = new AnnotatedMethodImpl($this, $method);
        }
    }

    public function getAnnotation($annotationType) {
        return isset($this->annotations[$annotationType])? $this->annotations[$annotationType] : null;
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function isAnnotationPresent($annotationType) {
        return isset($this->annotations[$annotationType]) && $this->annotations[$annotationType] != null;
    }

    public function getBaseType() {
        return $this->className;
    }

    public function getConstructor() {
        return $this->constructor;
    }

    public function getFields() {
        if($this->fields == null) {
            $this->initFields();
        }
        
        return $this->fields;
    }

    public function getMethods() {
        if($this->methods == null) {
            $this->initMethods();
        }
        
        return $this->methods;
    }

    public function getPHPClass() {
        return $this->reflectionClass;
    }

    public function getTypeClosure() {
        return ReflectionUtil::getClassNames($this->reflectionClass);
    }

    public function getMethodsWithAnnotation($annotationClass) {
        if($this->methods == null) {
            $this->initMethods();
        }
        
        $methods = array();

        foreach($this->methods as $method) {
            if($method->isAnnotationPresent($annotationClass)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }
    
    public function getMethodsWithAnnotationOnFirstParameter($annotationClass) {
        if($this->methods == null) {
            $this->initMethods();
        }
        
        $methods = array();

        foreach($this->methods as $method) {
            $params = $method->getParameters();
            if(count($params) > 0 && $params[0]->isAnnotationPresent($annotationClass)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }
    
    public function getFieldsWithAnnotation($annotationClass) {
        if($this->fields == null) {
            $this->initFields();
        }
        
        $fields = array();
        
        foreach($this->fields as $field) {
            if($field->isAnnotationPresent($annotationClass)) {
                $fields[] = $field;
            }
        }
        
        return $fields;
    }
}
