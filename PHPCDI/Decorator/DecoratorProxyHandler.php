<?php

namespace PHPCDI\Decorator;

use PHPCDI\Proxy\MethodHandler;

class DecoratorProxyHandler implements MethodHandler {
    private $delegate;
    
    public function __construct($delegate) {
        $this->delegate = $delegate;
    }

    public function invoke($proxy, $declaredMethod, $overriddenMethod, array $args) {
        \call_user_method_array($declaredMethod['method'], $this->delegate, $args);
    }
}

