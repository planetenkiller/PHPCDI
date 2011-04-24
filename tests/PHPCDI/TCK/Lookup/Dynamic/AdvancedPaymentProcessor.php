<?php

namespace PHPCDI\TCK\Lookup\Dynamic;

/**
 * @PHPCDI\TCK\Lookup\Dynamic\PayBy("CHEQUE")
 * @ApplicationScoped
 */
class AdvancedPaymentProcessor implements AsynchronousPaymentProcessor {
    private $value;
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}

