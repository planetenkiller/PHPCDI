<?php

namespace PHPCDI\API\Event;

interface ProcessProducer {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedMember
     */
    public function getAnnotatedMember();
    
    /**
     * @return \PHPCDI\API\Inject\SPI\Producer
     */
    public function getProducer();
    
    
    public function setProducer(\PHPCDI\API\Inject\SPI\Producer $producer);
    
    
    public function addDefinitionError(\Exception $e);
}

