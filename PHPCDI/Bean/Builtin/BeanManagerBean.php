<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Inject\SPI\Bean;

class BeanManagerBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Bean\BeanManager 
     */
    private $beanManager;
    
    public function __construct(\PHPCDI\Bean\BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
       return $this->beanManager;
    }

    public function destroy($instance, $creationalContext) {
    }

    public function getBeanClass() {
        return 'PHPCDI\API\Inject\SPI\BeanManager';
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
        return array('PHPCDI\API\Inject\SPI\BeanManager', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "BeanManager Bean of class PHPCDI\API\Inject\SPI\BeanManager";
    }
}
