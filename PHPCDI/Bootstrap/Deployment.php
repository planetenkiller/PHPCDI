<?php

namespace PHPCDI\Bootstrap;

/**
 * Default implementation of Deployment
 */
class Deployment implements \PHPCDI\API\Bootstrap\Deployment {
    private $rootBundles = array();
    private $extensions = array();

    public function addClassBundle(\PHPCDI\API\Bootstrap\ClassBundle $bundle) {
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
