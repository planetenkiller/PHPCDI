<?php

namespace PHPCDI\Bean;

use PHPCDI\API\Inject\SPI\AnnotatedMethod;
use PHPCDI\API\Inject\SPI\Bean;

class ProducerMethod extends AbstractProducer {

    /**
     * @var BeanManager
     */
    private $beanManager;
    
    /**
     * @var PHPCDI\API\Inject\SPI\AnnotatedMethod 
     */
    private $disposer;

    public function __construct(Bean $declaringBean, AnnotatedMethod $method, $disposer, BeanManager $beanManager) {
        $methodName = $method->getPHPMember()->name;
        $ij = \PHPCDI\Util\Beans::getParameterInjectionPoints($declaringBean, $method);
        parent::__construct($declaringBean, $method, $ij);
        $this->disposer = $disposer;
        $this->beanManager = $beanManager;
        $this->setProducer(new \PHPCDI\Injection\MethodProducer($this));
    }

    public function destroy($instance, $creationalContext) {
        $this->producer->dispose($instance);
        
        if($this->getScope() instanceof \PHPCDI\API\Inject\Dependent) {
            $creationalContext->release();
        }
    }
    
    public function getBeanManager() {
        return $this->beanManager;
    }
    
    public function getDisposer() {
        return $this->disposer;
    }
    
    public function __toString() {
        return "Producer method: [" . $this->declaringBean . ']->' . $this->member->getPHPMember()->name;
    }
}
