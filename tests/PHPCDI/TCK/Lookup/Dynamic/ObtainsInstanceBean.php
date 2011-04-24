<?php

namespace PHPCDI\TCK\Lookup\Dynamic;

class ObtainsInstanceBean {
    /**
     * @Inject @PHPCDI\TCK\Lookup\Dynamic\PayBy("CHEQUE")
     * @Instance("PHPCDI\TCK\Lookup\Dynamic\AsynchronousPaymentProcessor")
     * 
     * @var PHPCDI\API\Instance\Instance
     */
    private $paymentProcessor;
    
    /**
     * @Inject @Any
     * @Instance("PHPCDI\TCK\Lookup\Dynamic\PaymentProcessor")
     * 
     * @var PHPCDI\API\Instance\Instance
     */
    private $anyPaymentProcessor;
    
    public function getPaymentProcessor() {
        return $this->paymentProcessor;
    }
    
    public function getAnyPaymentProcessor() {
        return $this->anyPaymentProcessor;
    }
}

