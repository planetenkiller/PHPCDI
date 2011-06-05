<?php

namespace PHPCDI\Bean;

use PHPCDI\SPI\AnnotatedMember;
use PHPCDI\SPI\Bean;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;
use PHPCDI\API\Annotations;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\SPI\Producer;

abstract class AbstractProducer implements Bean {

    /**
     * @var string
     */
    private $declaringBeanClassName;

    /**
     * @var PHPCDI\SPI\Bean
     */
    protected $declaringBean;

    /**
     * @var PHPCDI\SPI\AnnotatedMember
     */
    protected $member;
    
    /**
     *
     * @var \PHPCDI\SPI\Producer
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

        $this->qualifiers = AnnotationUtil::getQualifiers($member);
        if(empty($this->qualifiers)) {
            $this->qualifiers[] = new Annotations\Any(array());
            $this->qualifiers[] = new Annotations\DefaultObj(array());
        }

        $this->stereotypes = AnnotationUtil::getStereotypes($member);
        $this->scope = AnnotationUtil::getScope($member);
    }
    
    
    public function create(CreationalContext $creationalContext) {
        $obj = $this->producer->produce($creationalContext);
        
        if($this->getScope() instanceof Annotations\Dependent) {
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
        return !ReflectionUtil::isPrimitiveType($this->member->getBaseType());
    }
    
    public function setProducer(Producer $producer) {
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
