<?php

namespace PHPCDI\Injection;

use PHPCDI\SPI\InjectionPoint;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\API\Annotations;

class ParameterInjectionPoint implements InjectionPoint {

    /**
     * @var \PHPCDI\SPI\Bean
     */
    private $bean;

    /**
     * @var \PHPCDI\SPI\AnnotatedParameter
     */
    private $paramter;

    private $qualifiers;

    public function __construct($bean, $paramter) {
        $this->bean = $bean;
        $this->paramter = $paramter;
        $this->qualifiers = AnnotationUtil::getQualifiers($this->paramter);
        
        if(empty($this->qualifiers)) {
            $this->qualifiers[] = new Annotations\DefaultObj(array());
        }
    }

    public function getType() {
        return $this->paramter->getBaseType();
    }

    public function getQualifiers() {
        return $this->qualifiers;
    }

    /**
     * @return \PHPCDI\SPI\Bean
     */
    public function getBean() {
        return $this->bean;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getMember() {
        return $this->paramter->getDeclaringCallable()->getPHPMember();
    }

    public function getAnnotated() {
        return $this->paramter;
    }

    public function isDelegate() {
        return $this->paramter->isAnnotationPresent(Annotations\Delegate::className());
    }

    public function isTransient() {
        return false;
    }
    
    public function __toString() {
        return "Method parameter injection point " . $this->paramter->getDeclaringCallable()->getDeclaringType()->getBaseType() . '->' . $this->paramter->getDeclaringCallable()->getPHPMember()->name . '(' . $this->paramter->getName() . ')';
    }
}
