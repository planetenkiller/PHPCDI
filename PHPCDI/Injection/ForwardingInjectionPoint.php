<?php

namespace PHPCDI\Injection;

use PHPCDI\API\Inject\SPI\InjectionPoint;

/**
 * An InjectionPoint that delegates all calls to an another InjectionPoint.
 */
abstract class ForwardingInjectionPoint implements InjectionPoint {
    
    /**
     * @return \PHPCDI\API\Inject\SPI\InjectionPoint
     */
    protected abstract function getDelegate();

    public function getAnnotated() {
        return $this->getDelegate()->getAnnotated();
    }

    public function getBean() {
        return $this->getDelegate()->getBean();
    }

    public function getMember() {
        return $this->getDelegate()->getMember();
    }

    public function getQualifiers() {
        return $this->getDelegate()->getQualifiers();
    }

    public function getType() {
        return $this->getDelegate()->getType();
    }

    public function isDelegate() {
        return $this->getDelegate()->isDelegate();
    }

    public function isTransient() {
        return $this->getDelegate()->isTransient();
    }
}

