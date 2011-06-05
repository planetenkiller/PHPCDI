<?php

namespace PHPCDI\API;

interface Instance extends \IteratorAggregate {
    public function get();
    
    /**
     * @return Instance
     */
    public function select(array $qualifiers);
    
    /**
     * @return Instance
     */
    public function selectInstance($subType, array $qualifiers);
    
    /**
     * @return boolean
     */
    public function isUnsatisfied();
    
    /**
     * @return boolean
     */
    public function isAmbiguous();
}

