<?php

namespace PHPCDI\SPI\Bootstrap\Impl;

use PHPCDI\SPI\Bootstrap\ClassBundle;

/**
 * Default implementation of Deployment
 */
class Deployment implements \PHPCDI\SPI\Bootstrap\Deployment {
    private $rootBundles = array();
    private $extensions = array();

    public function addClassBundle(ClassBundle $bundle) {
        $this->rootBundles[] = $bundle;
    }
    
    public function markAsExtension($className) {
        $this->extensions[] = $className;
    }

    public function getClassBundles() {
        return $this->rootBundles;
    }
    
    public function getBundleOfClass($classname) {
        foreach($this->rootBundles as $bundle) {
            if(in_array($classname, $bundle->getClasses())) {
                return $bundle;
            }
        }
        
        return null;
    }
    
    public function getExtensions() {
        return $this->extensions;
    }
}
