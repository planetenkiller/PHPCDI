<?php

namespace PHPCDI\API\Event;

interface ProcessProducerField extends ProcessBean {
    /**
     * @return \PHPCDI\SPI\AnnotatedField
     */
    public function getAnnotatedProducerField();
}

