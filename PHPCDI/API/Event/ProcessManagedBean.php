<?php

namespace PHPCDI\API\Event;

interface ProcessManagedBean extends ProcessBean {
    /**
     * @return \PHPCDI\SPI\AnnotatedType
     */
    public function getAnnotatedBeanClass();
}

