<?php

namespace PHPCDI\API\Event;

interface ProcessProducerMethod extends ProcessBean {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedMethod
     */
    public function getAnnotatedProducerMethod();
    
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedParameter
     */
    public function getAnnotatedDisposedParameter();

}

