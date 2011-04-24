<?php

namespace PHPCDI\Injection;

class InstanceFacadeInjectionPoint extends ForwardingInjectionPoint {
    private $delegate;
    private $qualifier;
    
    public function __construct(\PHPCDI\API\Inject\SPI\InjectionPoint $delegate, array $newQualifier) {
        $this->delegate = $delegate;
        $this->qualifier = \PHPCDI\Util\Beans::mergeQualifiers(parent::getQualifiers(), $newQualifier);
    }
    
    protected function getDelegate() {
        return $this->delegate;
    }
    
    public function getQualifiers() {
        return $this->qualifier;
    }
}

