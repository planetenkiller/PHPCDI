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
    }

    public function create($creationalContext) {
        $declaringBeanObj = $this->beanManager->getRefernce($this->declaringBean, $this->member->getBaseType(), $creationalContext);
        $reflectionMethod = $this->member->getPHPMember();

        $values = array();
        foreach ($this->getInjectionPoints() as $injection) {
            $values[] = $this->beanManager->getInjectableReference($injection, $creationalContext);
        }

        if(\count($values) > 0) {
            $obj = $reflectionMethod->invokeArgs($declaringBeanObj, $values);
        } else {
            $obj = $reflectionMethod->invoke($declaringBeanObj);
        }

        if($this->getScope() instanceof \PHPCDI\API\Inject\Dependent) {
            $creationalContext->release();
        }

        return $obj;
    }

    public function destroy($instance, $creationalContext) {
        if($this->disposer) {
            $declaringBeanObj = $this->beanManager->getRefernce($this->declaringBean, $this->member->getBaseType(), $creationalContext);
            $reflectionMethod = $this->disposer->getPHPMember();

            $injectionPoints = \PHPCDI\Util\Beans::getParameterInjectionPoints($this->declaringBean, $this->member);
            unset($injectionPoints[0]);// first injection point is the parameter with @Disposes

            $values = array($instance);
            foreach ($injectionPoints as $injection) {
                $values[] = $this->beanManager->getInjectableReference($injection, $ctx);
            }

            $reflectionMethod->invokeArgs($declaringBeanObj, $values);

            if($this->getScope() instanceof \PHPCDI\API\Inject\Dependent) {
                $creationalContext->release();
            }
        }
    }
    
    public function __toString() {
        return "Producer method: [" . $this->declaringBean . ']->' . $this->member->getPHPMember()->name;
    }
}
