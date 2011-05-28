<?php

namespace PHPCDI\API\Event;

interface ProcessProducerField extends ProcessBean {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedField
     */
    public function getAnnotatedProducerField();
}

