<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Inject\SPI\Bean;

class InjectionPointBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Bean\BeanManager 
     */
    private $beanManager;
    
    public function __construct(\PHPCDI\Bean\BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
       return $this->beanManager->getCurrentInjectionPoint();
    }

    public function destroy($instance, $creationalContext) {
    }

    public function getBeanClass() {
        return 'PHPCDI\API\Inject\SPI\InjectionPoint';
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
        return \PHPCDI\API\Inject\Dependent::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\API\Inject\SPI\InjectionPoint', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\API\Inject\SPI\InjectionPoint";
    }
}
