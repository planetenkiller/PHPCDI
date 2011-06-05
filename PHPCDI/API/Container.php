<?php

namespace PHPCDI\API;

use PHPCDI\SPI\BeanManager;
use PHPCDI\SPI\Bootstrap\Deployment;
use PHPCDI\SPI\Bootstrap\ClassBundle;

/**
 * Main Container for PHPCDI.
 */
class Container {
    /**
     * @var \PHPCDI\SPI\Bootstrap\Deployment
     */
    private $deployment;

    /**
     * @var \PHPCDI\SPI\BeanManager
     */
    private $rootManager;

    /**
     * @var \SplObjectStorage
     */
    private $classBundlesToManagers;

    /**
     * Do not call this constructor directly! The {@link \PHPCDI\API\Configuration} class builds
     * container instances.
     */
    public function __construct(Deployment $deployment, BeanManager $rootManager, \SplObjectStorage $classBundlesToManagers) {
        $this->deployment = $deployment;
        $this->rootManager = $rootManager;
        $this->classBundlesToManagers = $classBundlesToManagers;
    }

    /**
     * @param \PHPCDI\SPI\Bootstrap\ClassBundle $classBundle
     *
     * @return \PHPCDI\SPI\BeanManager bean manager instance or null
     */
    public function getManager(ClassBundle $classBundle) {
        if(isset($this->classBundlesToManagers[$classBundle])) {
            return $this->classBundlesToManagers[$classBundle];
        } else {
            return null;
        }
    }
}

