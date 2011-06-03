<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Inject\SPI\Bean;

class InstanceBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
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
            $annotation = $ij->getAnnotated()->getAnnotation(\PHPCDI\API\Inject\Instance::className());
            if($annotation == null || empty($annotation->value)) {
                throw new \PHPCDI\API\Inject\DefinitionException('Instance injection point [' . $ij . '] must declare its Instance data type with a @Instance annotation');
            }
            
            return new InstanceImpl($ij, $annotation->value, $this->beanManager, $creationalContext);
        }
    }

    public function destroy($instance, $creationalContext) {
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
        return array(new \PHPCDI\API\Inject\Any(array()));
    }

    public function getScope() {
        return \PHPCDI\API\Inject\Dependent::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return array('PHPCDI\API\Instance\Instance', 'mixed');
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Builtin Bean for class PHPCDI\API\Instance\Instance";
    }
}
