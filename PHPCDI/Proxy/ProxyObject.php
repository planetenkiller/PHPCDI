<?php

namespace PHPCDI\Proxy;

/**
 * Interface implemented by proxy classes.
 */
interface ProxyObject {
    /**
     * @return MethodHandler
     */
    public function getHandler();
    
    public function setHandler(MethodHandler $handler);
}

