<?php

namespace PHPCDI\Proxy;

/**
 * The interface implemented by the invocation handler of a proxy instance. 
 */
interface MethodHandler {
    public function invoke($proxy, $declaredMethod, $overriddenMethod, array $args);
}
