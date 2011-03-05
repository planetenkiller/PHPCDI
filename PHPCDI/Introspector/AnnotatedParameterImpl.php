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

        if(isset($this->annotations['PHPCDI\Util\PhpDoc\PhpDocParam'])) {
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
                $this->allTypes = \PHPCDI\Util\ReflectionUtil::getClassNames(new \ReflectionClass($this->baseType));
            } else {
                $this->baseType = 'mixed';
                $this->allTypes = array('mixed');
            }
        } else {
            $this->baseType = 'mixed';
            $this->allTypes = array('mixed');
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

