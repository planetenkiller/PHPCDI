<?php

namespace PHPCDI\TCK\Definition\Qualifier;

class Order {
    /**
     * @Inject
     */
    public function __construct(OrderProcessor $processor) {
    }
}

