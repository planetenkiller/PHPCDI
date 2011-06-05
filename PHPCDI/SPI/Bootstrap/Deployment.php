<?php

namespace PHPCDI\SPI\Bootstrap;

/**
 * Depolyment descriptior
 *
 * This class contains all available class bundles.
 */
interface Deployment {
    /**
     * @return ClassBundle[] all available class bundles
     */
    public function getClassBundles();
    
    /**
     * @return ClassBundle
     */
    public function getBundleOfClass($classname);
    
    public function getExtensions();
}
