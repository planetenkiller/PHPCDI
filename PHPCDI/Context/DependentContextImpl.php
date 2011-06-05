<?php

namespace PHPCDI\Context;

use PHPCDI\SPI\Context\Context;
use PHPCDI\SPI\Context\Contextual;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\API\Annotations;

/**
 * Context implementation of Dependent scope.
 */
class DependentContextImpl implements Context {

    public function get(Contextual $bean, CreationalContext $creationalContext = null) {
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
        return Annotations\Dependent::className();
    }

    public function isActive() {
        return true;
    }
}
