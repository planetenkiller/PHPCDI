<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Inject\SPI\Bean;

class EventBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Bean\BeanManager 
     */
    private $beanManager;
    
    public function __construct(\PHPCDI\Bean\BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
        $ij = $this->beanManager->getCurrentInjectionPoint();
        
        if($ij == null) {
            return null;
        } else {
            return new \PHPCDI\Event\EventImpl($ij, $this->beanManager);
        }
    }

    public function destroy($instance, $creationalContext) {
        $creationalContext->release();
    }

    public function getBeanClass() {
        return 'PHPCDI\Event\EventImpl';
    }

    public function getInjectionPoints() {
        return array();
    }

    public function getName() {
        return null;
    }

    public function getQualifiers() {
        return array(new \PHPCDI\API\Inject\Any(array()));
    }

    public function getScope() {
        return 'PHPCDI\API\Inject\Dependent';
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\API\Event\Event', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\API\Event\Event";
    }
}
