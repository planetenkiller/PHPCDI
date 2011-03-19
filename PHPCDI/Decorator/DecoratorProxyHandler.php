<?php

namespace PHPCDI\Decorator;

class DecoratorProxyHandler implements \PHPCDI\Proxy\MethodHandler {
    private $delegate;
    
    public function __construct($delegate) {
        $this->delegate = $delegate;
    }

    public function invoke($proxy, $declaredMethod, $overriddenMethod, array $args) {
        \call_user_method_array($declaredMethod['method'], $this->delegate, $args);
    }
}

