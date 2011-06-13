<?php

namespace PHPCDI\SPI\Bootstrap\Impl;

use PHPCDI\SPI\Bootstrap\ClassBundle;

class ClassListClassBundle implements ClassBundle {

    private $bundles;
    private $classes;
    private $id;

    public function __construct($id, $classes) {
        $this->bundles = array();
        $this->classes = $classes;
        $this->id = $id;
    }

    public function addClassBundle(ClassBundle $otherBundle) {
        $this->bundles[] = $otherBundle;
    }

    public function getClassBundles() {
        return $this->bundles;
    }

    public function getClasses() {
        return $this->classes;
    }

    public function getId() {
        return $this->id;
    }
}
