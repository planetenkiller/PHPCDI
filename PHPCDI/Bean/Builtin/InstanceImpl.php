<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Instance;

use PHPCDI\API\UnsatisfiedResolutionException;
use PHPCDI\Injection\InstanceFacadeInjectionPoint;

class InstanceImpl implements Instance {
    
    /**
     * @var \PHPCDI\SPI\InjectionPoint
     */
    private $ij;
    
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    /**
     * @var \PHPCDI\SPI\Context\CreationalContext
     */
    private $ctx;
    
    private $type;
    
    public function __construct($ij, $type, $beanManager, $ctx) {
        $this->ij = $ij;
        $this->beanManager = $beanManager;
        $this->ctx = $ctx;
        $this->type = $type;
    }

    public function get() {
        $bean = $this->beanManager->resolve($this->getBeans());
        
        if($bean == null) {
            throw new UnsatisfiedResolutionException(
                    'unsatisfied dependency on injection point [' . $this->ij . ']');
        }
        
        // InstanceFacadeInjectionPoint loads qualifiers via injection point
        $newIp = new InstanceFacadeInjectionPoint($this->ij, array()); 
        
        $this->beanManager->getInjectionPointStack()->push($newIp);
        
        $beanInstance = null;
        try {
            $beanInstance = $this->beanManager->getRefernce($bean, $this->type, $this->ctx);
        } catch (\Exception $e) {
            $this->beanManager->getInjectionPointStack()->pop();
            throw $e;
        }
        
        return $beanInstance;
    }

    public function getIterator() {
        $objects = array();
        
        foreach($this->getBeans() as $bean) {
            if(!$bean instanceof DynamicLookupUnsupported) {
                $objects[] = $this->beanManager->getRefernce(
                        $bean, 
                        $this->type, 
                        $this->beanManager->createCreationalContext($bean));
            }
        }
        
        return new \ArrayIterator($objects);
    }

    public function isAmbiguous() {
        return count($this->getBeans()) > 1;
    }

    public function isUnsatisfied() {
        return count($this->getBeans()) == 0;
    }

    public function select(array $qualifiers) {
        return $this->selectInstance($this->type, $qualifiers);
    }

    public function selectInstance($subType, array $qualifiers) {
        $ij = new InstanceFacadeInjectionPoint($this->ij, $qualifiers);
        return new InstanceImpl($ij, $subType, $this->beanManager, $this->ctx);
    }
    
    private function getBeans() {
        return $this->beanManager->getBeans($this->type, $this->ij->getQualifiers());
    }
}
