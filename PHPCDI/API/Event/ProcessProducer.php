<?php

namespace PHPCDI\API\Event;

use PHPCDI\SPI\Producer;

interface ProcessProducer {
    /**
     * @return \PHPCDI\SPI\AnnotatedMember
     */
    public function getAnnotatedMember();
    
    /**
     * @return \PHPCDI\SPI\Producer
     */
    public function getProducer();
    
    
    public function setProducer(Producer $producer);
    
    
    public function addDefinitionError(\Exception $e);
}

