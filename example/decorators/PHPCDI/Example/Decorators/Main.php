<?php

namespace PHPCDI\Example\Decorators;

class Main {
    /**
     * @Inject
     * @var PHPCDI\Example\Decorators\Logger
     */
    private $logger;
    
    public function main() {
        $this->logger->log('hello from main via logger', null);
    }
}
