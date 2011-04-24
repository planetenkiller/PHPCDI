<?php

namespace PHPCDI\API\Instance;

interface Instance extends \IteratorAggregate {
    public function get();
    
    /**
     * @return PHPCDI\API\Instance\Instance
     */
    public function select(array $qualifiers);
    
    /**
     * @return PHPCDI\API\Instance\Instance
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

