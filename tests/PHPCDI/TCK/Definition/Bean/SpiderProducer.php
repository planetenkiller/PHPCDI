<?php

namespace PHPCDI\TCK\Definition\Bean;

class SpiderProducer {
    /**
     * @Produces @PHPCDI\TCK\Definition\Bean\Tame
     * @return PHPCDI\TCK\Definition\Bean\WolfSpider 
     */
    public function makeASpider() {
        return new WolfSpider();
    }
    
    /**
     * @Produces
     * @return int 
     */
    public function getWolfSpiderSize() {
        return 4;
    }
}

