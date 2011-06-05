<?php

namespace PHPCDI\SPI\Context;

use PHPCDI\SPI\Context\CreationalContext;

/**
 * 
 */
interface Contextual {
    public function create(CreationalContext $creationalContext);

    public function destroy($instance, CreationalContext $creationalContext);
}

