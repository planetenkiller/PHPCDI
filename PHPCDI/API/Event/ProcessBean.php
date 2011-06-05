<?php

namespace PHPCDI\API\Event;

interface ProcessBean {
    /**
     * @return \PHPCDI\SPI\Annotated
     */
    public function getAnnotated();
    
    /**
     * @return \PHPCDI\SPI\Bean
     */
    public function getBean();
    
    public function addDefinitionError(\Exception $e);
}

