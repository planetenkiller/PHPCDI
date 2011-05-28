<?php

namespace PHPCDI\API\Event;

interface BeforeBeanDiscovery {
    public function addAnnotatedType(\PHPCDI\API\Inject\SPI\AnnotatedType $annotatedType);
}

