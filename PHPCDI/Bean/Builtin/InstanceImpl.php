<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Instance\Instance;

class InstanceImpl implements Instance {
    
    /**
     * @var \PHPCDI\API\Inject\SPI\InjectionPoint
     */
    private $ij;
    
    /**
     * @var \PHPCDI\Bean\BeanManager
     */
    private $beanManager;
    
    /**
     * @var \PHPCDI\API\Context\SPI\CreationalContext 
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
            throw new \PHPCDI\API\UnsatisfiedResolutionException(
                    'unsatisfied dependency on injection point [' . $this->ij . ']');
        }
        
        $newIp = new \PHPCDI\Injection\InstanceFacadeInjectionPoint($this->ij, array()); // InstanceFacadeInjectionPoint loads qualifiers via injectionpoint
        
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
        $ij = new \PHPCDI\Injection\InstanceFacadeInjectionPoint($this->ij, $qualifiers);
        return new InstanceImpl($ij, $subType, $this->beanManager, $this->ctx);
    }
    
    private function getBeans() {
        return $this->beanManager->getBeans($this->type, $this->ij->getQualifiers());
    }
}
