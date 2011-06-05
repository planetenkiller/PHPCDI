<?php

namespace PHPCDI\SPI;

use PHPCDI\SPI\Context\CreationalContext;

/**
 *
 */
interface Producer {
   public function produce(CreationalContext $creationalContext);
   public function dispose($instance);
   
   /**
    * @return \PHPCDI\SPI\InjectionPoint[] 
    */
   public function getInjectionPoints();
}

