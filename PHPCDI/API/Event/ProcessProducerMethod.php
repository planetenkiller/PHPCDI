<?php

namespace PHPCDI\API\Event;

interface ProcessProducerMethod extends ProcessBean {
    /**
     * @return \PHPCDI\SPI\AnnotatedMethod
     */
    public function getAnnotatedProducerMethod();
    
    /**
     * @return \PHPCDI\SPI\AnnotatedParameter
     */
    public function getAnnotatedDisposedParameter();

}

