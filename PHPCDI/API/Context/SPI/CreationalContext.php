<?php

namespace PHPCDI\API\Context\SPI;

/**
 *
 */
interface CreationalContext {
    public function push($incompleteInstance);
    public function release();
}
