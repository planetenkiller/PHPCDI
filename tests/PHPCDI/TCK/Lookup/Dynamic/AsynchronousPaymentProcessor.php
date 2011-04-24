<?php

namespace PHPCDI\TCK\Lookup\Dynamic;

interface AsynchronousPaymentProcessor extends PaymentProcessor {
    public function getValue();
    public function setValue($value);
}

