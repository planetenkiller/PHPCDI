<?php

namespace PHPCDI\Extensions\Doctrine2;

use PHPCDI\SPI\Bean;
use PHPCDI\SPI\BeanManager;
use PHPCDI\API\Annotations;
use PHPCDI\SPI\Context\CreationalContext;

class RepositoryBean implements Bean {
    
    private $class;
    private $entity;
    
    /**
     * @var PHPCDI\SPI\BeanManager
     */
    private $beanManager;
    
    public function __construct($class, $entity, BeanManager $beanManager) {
        $this->class = $class;
        $this->entity = $entity;
        $this->beanManager = $beanManager;
    }

    public function create(CreationalContext $creationalContext) {
        $ctx = $this->beanManager->createCreationalContext($this);
        $emBean = $this->beanManager->resolve($this->beanManager->getBeans('Doctrine\ORM\EntityManager', array()));
        $em = $this->beanManager->getRefernce($emBean, 'Doctrine\ORM\EntityManager', $ctx);
        $ctx->release();
        
        return $em->getRepository($this->entity);
    }

    public function destroy($instance, CreationalContext $creationalContext) {
    }

    public function getBeanClass() {
        return $this->class;
    }

    public function getInjectionPoints() {
        return array();
    }

    public function getName() {
        return null;
    }

    public function getQualifiers() {
        return array(Annotations\DefaultObj::newInstance(), Annotations\Any::newInstance());
    }

    public function getScope() {
        return Annotations\ApplicationScoped::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array($this->class);
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
}

