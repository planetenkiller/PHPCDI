<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\API\Annotations;
use PHPCDI\API\DefinitionException;
use PHPCDI\SPI\Context\CreationalContext;

class InstanceBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\SPI\BeanManager
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
            $annotation = $ij->getAnnotated()->getAnnotation(Annotations\Instance::className());
            if($annotation == null || empty($annotation->value)) {
                throw new DefinitionException('Instance injection point [' . $ij . '] must declare its Instance data type with a @Instance annotation');
            }
            
            return new InstanceImpl($ij, $annotation->value, $this->beanManager, $creationalContext);
        }
    }

    public function destroy($instance, CreationalContext $creationalContext) {
        $creationalContext->release();
    }

    public function getBeanClass() {
        return 'PHPCDI\Bean\Builtin\InstanceImpl';
    }

    public function getInjectionPoints() {
        return array();
    }

    public function getName() {
        return null;
    }

    public function getQualifiers() {
        return array(Annotations\Any::newInstance());
    }

    public function getScope() {
        return Annotations\Dependent::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\API\Instance', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\API\Instance";
    }
}
