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
    
    /**
     *
     * @var \PHPCDI\API\Inject\SPI\Producer
     */
    protected $producer;

    protected $injectionpoints;
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
    
    
    public function create($creationalContext) {
        $obj = $this->producer->produce($creationalContext);
        
        if($this->getScope() instanceof \PHPCDI\API\Inject\Dependent) {
            $creationalContext->release();
        }
        
        return $obj;
    }

    public function getBeanClass() {
        return $this->declaringBeanClassName;
    }

    public function getInjectionPoints() {
        return $this->producer->getInjectionPoints();
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
        return !\PHPCDI\Util\ReflectionUtil::isPrimitiveType($this->member->getBaseType());
    }
    
    public function setProducer(\PHPCDI\API\Inject\SPI\Producer $producer) {
        $this->producer = $producer;
    }
    
    public function getProducer() {
        return $this->producer;
    }
    
    public function getPhpCdiInjectionPoints() {
        return $this->injectionpoints;
    }
    
    public function getMember() {
        return $this->member;
    }
    
    public function getDeclaringBean() {
        return $this->declaringBean;
    }
}
