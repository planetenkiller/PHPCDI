<?php

namespace PHPCDI\Bootstrap;

/**
 * Default implementation of Deployment
 */
class Deployment implements \PHPCDI\API\Bootstrap\Deployment {
    private $rootBundles = array();

    public function addClassBundle(\PHPCDI\API\Bootstrap\ClassBundle $bundle) {
        $this->rootBundles[] = $bundle;
    }

    public function getClassBundles() {
        return $this->rootBundles;
    }
}
