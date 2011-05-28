<?php

namespace PHPCDI\Extensions\Doctrine2;

use PHPCDI\API\Inject\SPI\Bean;

class RepositoryBean implements Bean {
    
    private $class;
    private $entity;
    
    /**
     * @var PHPCDI\API\Inject\SPI\BeanManager
     */
    private $beanManager;
    
    public function __construct($class, $entity, \PHPCDI\API\Inject\SPI\BeanManager $beanManager) {
        $this->class = $class;
        $this->entity = $entity;
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
        $ctx = $this->beanManager->createCreationalContext($this);
        $emBean = $this->beanManager->resolve($this->beanManager->getBeans('Doctrine\ORM\EntityManager', array()));
        $em = $this->beanManager->getRefernce($emBean, 'Doctrine\ORM\EntityManager', $ctx);
        $ctx->release();
        
        return $em->getRepository($this->entity);
    }

    public function destroy($instance, $creationalContext) {
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
        return array(\PHPCDI\API\Inject\DefaultObj::newInstance(), \PHPCDI\API\Inject\Any::newInstance());
    }

    public function getScope() {
        return 'PHPCDI\API\Inject\ApplicationScoped';
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

