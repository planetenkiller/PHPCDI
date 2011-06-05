<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\API\Annotations as Annotations;
use PHPCDI\SPI\Context\CreationalContext;

class BeanManagerBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Manager\BeanManager 
     */
    private $beanManager;
    
    public function __construct(BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create(CreationalContext $creationalContext) {
       return $this->beanManager;
    }

    public function destroy($instance, CreationalContext $creationalContext) {
    }

    public function getBeanClass() {
        return 'PHPCDI\SPI\BeanManager';
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
        return array('PHPCDI\SPI\BeanManager', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "BeanManager Bean of class PHPCDI\SPI\BeanManager";
    }
}
