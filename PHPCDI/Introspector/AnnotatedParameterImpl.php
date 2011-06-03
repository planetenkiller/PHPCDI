<?php

namespace PHPCDI\Introspector;

class AnnotatedParameterImpl implements \PHPCDI\API\Inject\SPI\AnnotatedParameter {

    private $reflectionParameter;
    private $method;
    private $annotations;
    private $baseType;
    private $allTypes;

    public function __construct(\PHPCDI\API\Inject\SPI\AnnotatedCallable $method, \ReflectionParameter $parameter) {
        $this->reflectionParameter = $parameter;
        $this->method = $method;
        $this->annotations = \PHPCDI\Util\Annotations::reader()->getMethodAnnotations($method->getPHPMember());

        if($parameter->getClass() != null) {
            $this->baseType = $parameter->getClass()->name;
            $this->allTypes = \PHPCDI\Util\ReflectionUtil::getClassNames(new \ReflectionClass($this->baseType));
        } else if($parameter->isArray()) {
            $this->baseType = 'array';
            $this->allTypes = array('array', 'mixed');
        } else if(isset($this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'])) {
            $paramAnnotation = null;
            if(\is_array($this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'])) {
                foreach($this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'] as $param) {
                    if($param->name == $parameter->name) {
                        $paramAnnotation = $param;
                    }
                }
            } else if($this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam']->name == $parameter->name) {
                $paramAnnotation = $this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'];
            }

            if($paramAnnotation != null) {
                $this->baseType = $paramAnnotation->type;
                $this->baseType = \PHPCDI\Util\ReflectionUtil::resolveRelativeClassName($this->baseType, $parameter->getDeclaringClass());
                $this->allTypes = \PHPCDI\Util\ReflectionUtil::getClassNames($this->baseType);
                $this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'] = $paramAnnotation;
            } else {
                $this->baseType = 'mixed';
                $this->allTypes = array('mixed');
            }
        } else {
            $this->baseType = 'mixed';
            $this->allTypes = array('mixed');
        }
        
        if(isset($this->annotations[\PHPCDI\API\Inject\P::className()])) {
            // extract annotations for this parameter
            $paramAnnotation = null;
            if(\is_array($this->annotations[\PHPCDI\API\Inject\P::className()])) {
                foreach($this->annotations[\PHPCDI\API\Inject\P::className()] as $param) {
                    if($param->name == $parameter->name) {
                        $paramAnnotation = $param;
                    }
                }
            } else if($this->annotations[\PHPCDI\API\Inject\P::className()]->name == $parameter->name) {
                $paramAnnotation = $this->annotations[\PHPCDI\API\Inject\P::className()];
            }
            
            // remove all annotaton holder annotations
            unset($this->annotations[\PHPCDI\API\Inject\P::className()]);
            
            if($paramAnnotation != null) {
                // add parameter annotations as first level annotations
                foreach($paramAnnotation->value as $annotation) {
                    $this->annotations[\get_class($annotation)] = $annotation;
                }
            }
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

    public function getDeclaringCallable() {
        return $this->method;
    }

    public function getName() {
        return $this->reflectionParameter->name;
    }

    public function getPosition() {
        return $this->reflectionParameter->getPosition();
    }

    public function getTypeClosure() {
        return $this->allTypes;
    }
}

