<?php

namespace PHPCDI\Injection;

/**
 *
 */
class FieldInjectionPoint implements \PHPCDI\API\Inject\SPI\InjectionPoint {

    /**
     * @var \PHPCDI\API\Inject\SPI\Bean
     */
    private $bean;

    /**
     * @var \PHPCDI\API\Inject\SPI\AnnotatedField
     */
    private $paramter;

    private $qualifiers;

    public function __construct($bean, $paramter) {
        $this->bean = $bean;
        $this->paramter = $paramter;
        $this->qualifiers = \PHPCDI\Util\Annotations::getQualifiers($this->paramter);
    }

    public function getType() {
        return $this->paramter->getBaseType();
    }

    public function getQualifiers() {
        return $this->qualifiers;
    }

    /**
     * @return \PHPCDI\API\Inject\SPI\Bean
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
        return false;
    }

    public function isTransient() {
        return false;
    }

    public function inject($declaringInstance, \PHPCDI\Bean\BeanManager $mgr, \PHPCDI\API\Context\SPI\CreationalContext $ctx) {
        $objectToInject = $mgr->getInjectableReference($this, $ctx);
        $reflectionField = $this->getMember();
        $reflectionField->setAccessible(true);
        $reflectionField->setValue($declaringInstance, $objectToInject);
    }
}
