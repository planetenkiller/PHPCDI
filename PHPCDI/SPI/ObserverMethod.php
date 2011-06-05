<?php

namespace PHPCDI\SPI;

interface ObserverMethod {
    const RECEPTION_IF_EXISTS = 1;
    const RECEPTION_ALWAYS = 2;


    /**
     * @return string
     */
    public function getBeanClass();
    
    /**
     * @return string
     */
    public function getObservedType();
    
    /**
     * @return string
     */
    public function getObservedTypeFilter();
    
    /**
     * @return array of annotation objects or strings
     */
    public function getObservedQualifiers();
    
    /**
     * @return integer RECEPTION_IF_EXISTS or RECEPTION_ALWAYS
     */
    public function getReception();

    /**
     * @param mixed $eventData
     */
    public function notify($eventData);
}
