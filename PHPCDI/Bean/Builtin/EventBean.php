<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\Event\EventImpl;
use PHPCDI\API\Annotations as Annotations;
use PHPCDI\SPI\Context\CreationalContext;

class EventBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Manager\BeanManager$
     */
    private $beanManager;
    
    public function __construct(BeanManager $beanManager) {
        $this->beanManager = $beanManager;
    }

    public function create(CreationalContext $creationalContext) {
        $ij = $this->beanManager->getCurrentInjectionPoint();
        
        if($ij == null) {
            return null;
        } else {
            return new EventImpl($ij, $this->beanManager);
        }
    }

    public function destroy($instance, CreationalContext $creationalContext) {
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
        return array(new Annotations\Any(array()));
    }

    public function getScope() {
        return Annotations\Dependent::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\API\Event', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\API\Event";
    }
}
