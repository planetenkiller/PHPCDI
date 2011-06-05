<?php

namespace PHPCDI\Injection;

use PHPCDI\SPI\InjectionPoint;
use PHPCDI\Util\Beans as BeanUtil;

class EventFacadeInjectionPoint extends ForwardingInjectionPoint {
    private $delegate;
    private $qualifier;
    
    public function __construct(InjectionPoint $delegate, array $newQualifier) {
        $this->delegate = $delegate;
        $this->qualifier = BeanUtil::mergeQualifiers(parent::getQualifiers(), $newQualifier);
    }
    
    protected function getDelegate() {
        return $this->delegate;
    }
    
    public function getQualifiers() {
        return $this->qualifier;
    }
}

