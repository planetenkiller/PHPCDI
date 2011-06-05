<?php

namespace PHPCDI\SPI\Bootstrap;

/**
 * A bundle of classes.
 */
interface ClassBundle {
    /**
     * @return string unique id of this class bundle.
     */
    public function getId();

    /**
     * @return array all class names in this bundle.
     */
    public function getClasses();

    /**
     * @return array other available class bundles.
     */
    public function getClassBundles();
}

