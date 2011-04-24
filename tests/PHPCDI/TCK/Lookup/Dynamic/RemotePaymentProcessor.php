<?php

namespace PHPCDI\TCK\Lookup\Dynamic;

/**
 * @PHPCDI\TCK\Lookup\Dynamic\PayBy("CREDIT_CARD")
 * @ApplicationScoped
 */
class RemotePaymentProcessor implements AsynchronousPaymentProcessor {
    private $value;
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}

