<?php

namespace PHPCDI\API\Event;

use PHPCDI\SPI\AnnotatedType;

interface BeforeBeanDiscovery {
    public function addAnnotatedType(AnnotatedType $annotatedType);
}

