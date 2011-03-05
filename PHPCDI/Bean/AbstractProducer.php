<?php

namespace PHPCDI\Bean;

use PHPCDI\API\Inject\SPI\AnnotatedMember;
use PHPCDI\API\Inject\SPI\Bean;

abstract class AbstractProducer implements \PHPCDI\API\Inject\SPI\Bean {

    /**
     * @var string
     */
    private $declaringBeanClassName;

    /**
     * @var PHPCDI\API\Inject\SPI\Bean
     */
    protected $declaringBean;

    /**
     * @var PHPCDI\API\Inject\SPI\AnnotatedMember
     */
    protected $member;

    private $injectionpoints;
    private $qualifiers;
    private $stereotypes;
    private $scope;
    
    public function __construct(Bean $declaringBean, AnnotatedMember $member, array $injectionPoints) {
        $this->declaringBean = $declaringBean;
        $this->member = $member;
        $this->declaringBeanClassName = $member->getDeclaringType()->getBaseType();
        $this->injectionpoints = $injectionPoints;

        $this->qualifiers = \PHPCDI\Util\Annotations::getQualifiers($member);
        if(empty($this->qualifiers)) {
            $this->qualifiers[] = new \PHPCDI\API\Inject\Any(array());
            $this->qualifiers[] = new \PHPCDI\API\Inject\DefaultObj(array());
        }

        $this->stereotypes = \PHPCDI\Util\Annotations::getStereotypes($member);
        $this->scope = \PHPCDI\Util\Annotations::getScope($member);
    }

    public function getBeanClass() {
        return $this->declaringBeanClassName;
    }

    public function getInjectionPoints() {
        return $this->injectionpoints;
    }

    public function getName() {
        return $this->member->getPHPMember()->name;
    }

    public function getQualifiers() {
        return $this->qualifiers;
    }

    public function getScope() {
        return $this->scope;
    }

    public function getStereotypes() {
        return $this->stereotypes;
    }

    public function getTypes() {
        return $this->member->getTypeClosure();
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
}
