<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface Producer {
   public function produce($CreationalContext);
   public function dispose($instance);
   public function getInjectionPoints();
}

