<?php

namespace PHPCDI\Injection;

use PHPCDI\SPI\Producer;
use PHPCDI\Bean\ProducerField;
use PHPCDI\SPI\Context\CreationalContext;

class FieldProducer implements Producer {
    private $producer;
    
    public function __construct(ProducerField $producer) {
        $this->producer = $producer;
    }

    public function dispose($instance) {
    }

    public function getInjectionPoints() {
        return $this->producer->getPhpCdiInjectionPoints();
    }

    public function produce(CreationalContext $creationalContext) {
        $declaringBeanObj = $this->producer->getBeanManager()->getRefernce($this->producer->getDeclaringBean(), $this->producer->getMember()->getBaseType(), $creationalContext);
        $reflectionProperty = $this->producer->getMember()->getPHPMember();
        $reflectionProperty->setAccessible(true);

        $obj = $reflectionMethod->getValue($declaringBeanObj);

        return $obj;
    }
}

