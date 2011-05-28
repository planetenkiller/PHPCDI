<?php

namespace PHPCDI\Injection;

class MethodProducer implements \PHPCDI\API\Inject\SPI\Producer {
    private $producer;
    
    public function __construct(\PHPCDI\Bean\ProducerMethod $producer) {
        $this->producer = $producer;
    }

    public function dispose($instance) {
        if($this->producer->getDisposer()) {
            $creationalContext = $this->producer->getBeanManager()->createCreationalContext($this->producer->getDeclaringBean());
            $declaringBeanObj = $this->producer->getBeanManager()->getRefernce($this->producer->getDeclaringBean(), $this->producer->getMember()->getBaseType(), $creationalContext);
            $reflectionMethod = $this->producer->getDisposer()->getPHPMember();

            $injectionPoints = \PHPCDI\Util\Beans::getParameterInjectionPoints($this->producer->getDeclaringBean(), $this->producer->getMember());
            unset($injectionPoints[0]);// first injection point is the parameter with @Disposes

            $values = array($instance);
            foreach ($injectionPoints as $injection) {
                $values[] = $this->producer->getBeanManager()->getInjectableReference($injection, $ctx);
            }

            $reflectionMethod->invokeArgs($declaringBeanObj, $values);
            $creationalContext->release();
        }
    }

    public function getInjectionPoints() {
        return $this->producer->getPhpCdiInjectionPoints();
    }

    public function produce($creationalContext) {
        $declaringBeanObj = $this->producer
                                    ->getBeanManager()
                                    ->getRefernce($this->producer->getDeclaringBean(), 
                                                  $this->producer->getMember()->getBaseType(), 
                                                  $creationalContext);
        $reflectionMethod = $this->producer->getMember()->getPHPMember();

        $values = array();
        foreach ($this->getInjectionPoints() as $injection) {
            $values[] = $this->producer
                                ->getBeanManager()
                                ->getInjectableReference($injection, $creationalContext);
        }

        if(\count($values) > 0) {
            $obj = $reflectionMethod->invokeArgs($declaringBeanObj, $values);
        } else {
            $obj = $reflectionMethod->invoke($declaringBeanObj);
        }

        return $obj;
    }
}

