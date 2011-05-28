<?php

namespace PHPCDI\API\Event;

interface ProcessManagedBean extends ProcessBean {
    /**
     * @return \PHPCDI\API\Inject\SPI\AnnotatedType
     */
    public function getAnnotatedBeanClass();
}

