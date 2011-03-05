<?php

namespace PHPCDI\Context;

use PHPCDI\API\Context\SPI\Contextual;
use PHPCDI\API\Context\SPI\CreationalContext;

class ContextualInstance {
    /**
     * @var \PHPCDI\API\Context\SPI\Contextual
     */
    private $contextual;

    private $instance;

    /**
     * @var \PHPCDI\API\Context\SPI\CreationalContext
     */
    private $creationalContext;

    public function __construct(Contextual $contextual, $instance, CreationalContext $creationalContext) {
        $this->contextual = $contextual;
        $this->instance = $instance;
        $this->creationalContext = $creationalContext;
    }

    /**
     * @return \PHPCDI\API\Context\SPI\Contextual
     */
    public function getContextual() {
        return $this->contextual;
    }

    public function getInstance() {
        return $this->instance;
    }

    /**
     * @return \PHPCDI\API\Context\SPI\CreationalContext
     */
    public function getCreationalContext() {
        return $this->creationalContext;
    }
}
