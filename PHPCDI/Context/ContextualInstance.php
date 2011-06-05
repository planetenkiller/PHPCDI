<?php

namespace PHPCDI\Context;

use PHPCDI\SPI\Context\Contextual;
use PHPCDI\SPI\Context\CreationalContext;

class ContextualInstance {
    /**
     * @var \PHPCDI\SPI\Context\Contextual
     */
    private $contextual;

    private $instance;

    /**
     * @var \PHPCDI\SPI\Context\CreationalContext
     */
    private $creationalContext;

    public function __construct(Contextual $contextual, $instance, CreationalContext $creationalContext) {
        $this->contextual = $contextual;
        $this->instance = $instance;
        $this->creationalContext = $creationalContext;
    }

    /**
     * @return \PHPCDI\SPI\Context\Contextual
     */
    public function getContextual() {
        return $this->contextual;
    }

    public function getInstance() {
        return $this->instance;
    }

    /**
     * @return \PHPCDI\SPI\Context\CreationalContext
     */
    public function getCreationalContext() {
        return $this->creationalContext;
    }
}
