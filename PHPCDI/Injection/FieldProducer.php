<?php

namespace PHPCDI\Injection;

class FieldProducer implements \PHPCDI\API\Inject\SPI\Producer {
    private $producer;
    
    public function __construct(\PHPCDI\Bean\ProducerField $producer) {
        $this->producer = $producer;
    }

    public function dispose($instance) {
    }

    public function getInjectionPoints() {
        return $this->producer->getPhpCdiInjectionPoints();
    }

    public function produce($creationalContext) {
        $declaringBeanObj = $this->producer->getBeanManager()->getRefernce($this->producer->getDeclaringBean(), $this->producer->getMember()->getBaseType(), $creationalContext);
        $reflectionProperty = $this->producer->getMember()->getPHPMember();
        $reflectionProperty->setAccessible(true);

        $obj = $reflectionMethod->getValue($declaringBeanObj);

        return $obj;
    }
}

