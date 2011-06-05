<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\API\Annotations;

class InjectionPointBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    public function __construct(BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create(CreationalContext $creationalContext) {
       return $this->beanManager->getCurrentInjectionPoint();
    }

    public function destroy($instance, CreationalContext $creationalContext) {
    }

    public function getBeanClass() {
        return 'PHPCDI\SPI\InjectionPoint';
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
        return Annotations\Dependent::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\SPI\InjectionPoint', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\SPI\InjectionPoint";
    }
}
