<?php

namespace PHPCDI;

/**
 * Main Container for PHPCDI.
 */
class Container {
    /**
     * @var \PHPCDI\Bootstrap\Deployment
     */
    private $deployment;

    /**
     * @var Bean\BeanManager
     */
    private $rootManager;

    /**
     * @var \SplObjectStorage
     */
    private $classBundlesToManagers;

    /**
     * Do not call this constructor directly! The {@link \PHPCDI\Bootstrap\Configuration} create
     * the container instance.
     *
     * @param \PHPCDI\Bootstrap\Deployment $deployment
     * @param \PHPCDI\Bean\BeanManager $rootManager
     * @param \SplObjectStorage $classBundlesToManagers
     */
    public function __construct($deployment, Bean\BeanManager $rootManager, \SplObjectStorage $classBundlesToManagers) {
        $this->deployment = $deployment;
        $this->rootManager = $rootManager;
        $this->classBundlesToManagers = $classBundlesToManagers;
    }

    /**
     * @param API\Bootstrap\ClassBundle $classBundle
     *
     * @return API\Inject\SPI\BeanManager
     */
    public function getManager(API\Bootstrap\ClassBundle $classBundle) {
        if(isset($this->classBundlesToManagers[$classBundle])) {
            return $this->classBundlesToManagers[$classBundle];
        } else {
            return null;
        }
    }
}

