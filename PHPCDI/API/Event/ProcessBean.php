<?php

namespace PHPCDI\API\Event;

interface ProcessBean {
    /**
     * @return \PHPCDI\API\Inject\SPI\Annotated
     */
    public function getAnnotated();
    
    /**
     * @return \PHPCDI\API\Inject\SPI\Bean
     */
    public function getBean();
    
    public function addDefinitionError(\Exception $e);
}

