<?php

namespace PHPCDI\Introspector;

class AnnotatedTypeImpl implements \PHPCDI\API\Inject\SPI\AnnotatedType {

    private $className;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;
    private $annotations;
    private $constructor;
    private $fields;
    private $methods;

    public function __construct($className) {
        $this->className = $className;
        $this->reflectionClass = new \ReflectionClass($className);

        $this->annotations = \PHPCDI\Util\Annotations::reader()->getClassAnnotations($this->reflectionClass);

        if($this->reflectionClass->getConstructor() != null) {
            $this->constructor = new AnnotatedConstructorImpl($this, $this->reflectionClass->getConstructor());
        }

        $this->fields = array();
        foreach(\PHPCDI\Util\ReflectionUtil::getAllFields($this->reflectionClass) as $field) {
            $this->fields[] = new AnnotatedFieldImpl($this, $field);
        }

        $this->methods = array();
        foreach(\PHPCDI\Util\ReflectionUtil::getAllMethods($this->reflectionClass) as $method) {
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
        return $this->fields;
    }

    public function getMethods() {
        return $this->methods;
    }

    public function getPHPClass() {
        return $this->reflectionClass;
    }

    public function getTypeClosure() {
        return \PHPCDI\Util\ReflectionUtil::getClassNames($this->reflectionClass);
    }

    public function getMethodsWithAnnotation($annotationClass) {
        $methods = array();

        foreach($this->methods as $method) {
            if($method->isAnnotationPresent($annotationClass)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }
    
    public function getMethodsWithAnnotationOnFirstParameter($annotationClass) {
        $methods = array();

        foreach($this->methods as $method) {
            $params = $method->getParameters();
            if(count($params) > 0 && $params[0]->isAnnotationPresent($annotationClass)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }
}
