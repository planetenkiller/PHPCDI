<?php

namespace PHPCDI\Injection;

/**
 *
 */
class ParameterInjectionPoint implements \PHPCDI\API\Inject\SPI\InjectionPoint {

    /**
     * @var \PHPCDI\API\Inject\SPI\Bean
     */
    private $bean;

    /**
     * @var \PHPCDI\API\Inject\SPI\AnnotatedParameter
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
     * @return \ReflectionMethod
     */
    public function getMember() {
        return $this->paramter->getDeclaringCallable()->getPHPMember();
    }

    public function getAnnotated() {
        return $this->paramter;
    }

    public function isDelegate() {
        return $this->paramter->isAnnotationPresent('PHPCDI\API\Inject\Delegate');
    }

    public function isTransient() {
        return false;
    }
}
