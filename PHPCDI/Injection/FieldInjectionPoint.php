<?php

namespace PHPCDI\Injection;

use PHPCDI\SPI\InjectionPoint;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\API\Annotations;
use PHPCDI\Manager\BeanManager;
use PHPCDI\SPI\Context\CreationalContext;

class FieldInjectionPoint implements InjectionPoint {

    /**
     * @var \PHPCDI\SPI\Bean
     */
    private $bean;

    /**
     * @var \PHPCDI\SPI\AnnotatedField
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
     * @return \ReflectionProperty
     */
    public function getMember() {
        return $this->paramter->getPHPMember();
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

    public function inject($declaringInstance, BeanManager $mgr, CreationalContext $ctx) {
        $objectToInject = $mgr->getInjectableReference($this, $ctx);
        $reflectionField = $this->getMember();
        $reflectionField->setAccessible(true);
        $reflectionField->setValue($declaringInstance, $objectToInject);
    }
    
    public function __toString() {
        return "Class attribute injection point " . $this->paramter->getDeclaringType()->getBaseType() . '::' . $this->paramter->getPHPMember()->name;
    }
}
