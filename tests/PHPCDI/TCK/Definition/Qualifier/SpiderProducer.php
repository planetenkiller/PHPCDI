<?php

namespace PHPCDI\TCK\Definition\Qualifier;

class SpiderProducer {
    /**
     * @Produces @PHPCDI\TCK\Definition\Qualifier\Tame
     * @return PHPCDI\TCK\Definition\Qualifier\DefangedTarantula
     */
    public function produceTameTarantula() {
        return new DefangedTarantula();
    }
    
}
