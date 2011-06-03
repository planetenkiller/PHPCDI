<?php

namespace PHPCDI\Context;

use PHPCDI\API\Context\SPI\Context;

/**
 * Context implementation of Dependent scope.
 */
class DependentContextImpl implements Context {

    public function get($bean, $creationalContext = null) {
        if($creationalContext != null) {
            $obj = $bean->create($creationalContext);

            if($creationalContext instanceof CreationalContextImpl) {
                $creationalContext->addDependentInstance(new ContextualInstance($bean, $obj, $creationalContext));
            }

            return $obj;
        } else {
            return null;
        }
    }

    public function getScope() {
        return \PHPCDI\API\Inject\Dependent::className();
    }

    public function isActive() {
        return true;
    }
}
