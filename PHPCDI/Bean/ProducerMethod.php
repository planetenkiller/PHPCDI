<?php

namespace PHPCDI\Bean;

use PHPCDI\SPI\AnnotatedMethod;
use PHPCDI\SPI\Bean;
use PHPCDI\Util\Beans as BeanUtil;
use PHPCDI\Injection\MethodProducer;
use PHPCDI\API\Annotations;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\Manager\BeanManager;

class ProducerMethod extends AbstractProducer {

    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    /**
     * @var \PHPCDI\SPI\AnnotatedMethod
     */
    private $disposer;

    public function __construct(Bean $declaringBean, AnnotatedMethod $method, $disposer, BeanManager $beanManager) {
        $methodName = $method->getPHPMember()->name;
        $ij = BeanUtil::getParameterInjectionPoints($declaringBean, $method);
        parent::__construct($declaringBean, $method, $ij);
        $this->disposer = $disposer;
        $this->beanManager = $beanManager;
        $this->setProducer(new MethodProducer($this));
    }

    public function destroy($instance, CreationalContext $creationalContext) {
        $this->producer->dispose($instance);
        
        if($this->getScope() instanceof Annotations\Dependent) {
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
