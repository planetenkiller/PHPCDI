<?php

namespace PHPCDI\Example\Simple;

/**
 * @ApplicationScoped
 */
class Main {
    /**
     * @Inject
     * @var PHPCDI\Example\Simple\Store
     */
    private $store;

    /**
     * @Inject
     * @var PHPCDI\Example\Simple\Logger
     */
    private $logger;

    public function testStore1() {
        $this->store->put('a', array('a', 'b'));
    }

    public function testStore2() {
        $this->logger->log("Contents of a:" . \print_r($this->store->get('a'), true));
    }
}

