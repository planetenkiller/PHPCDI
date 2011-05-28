<?php

namespace PHPCDI\API\Bootstrap;

/**
 * Depolyment descriptior
 *
 * This class contains all available class bundles.
 */
interface Deployment {
    /**
     * @return array all available class bundles
     */
    public function getClassBundles();
    
    /**
     * @return ClassBundle
     */
    public function getBundleOfClass($classname);
    
    public function getExtensions();
}
