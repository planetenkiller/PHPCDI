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
}
