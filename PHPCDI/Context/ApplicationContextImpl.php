<?php

namespace PHPCDI\Context;

use PHPCDI\SPI\Context\Context;
use PHPCDI\SPI\Context\Contextual;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\API\Annotations;

/**
 * Context implementation of application scope.
 */
class ApplicationContextImpl implements Context {

    private $idCounter = 1;
    private $instances = array();
    private $idStore;

    public function __construct() {
        $this->idStore = new \SplObjectStorage();
    }

    public function get(Contextual $bean, CreationalContext $creationalContext = null) {
        $id = $this->getId($bean);

        if(isset($this->instances[$id])) {
            return $this->instances[$id]->getInstance();
        } else if($creationalContext != null) {
            $obj = $bean->create($creationalContext);
            if($obj != null) {
                $this->instances[$id] = new ContextualInstance($bean, $obj, $creationalContext);
            }
            return $obj;
        } else {
            return null;
        }
    }

    public function getScope() {
        return Annotations\ApplicationScoped::className();
    }

    public function isActive() {
        return true;
    }

    private function getId(Contextual $contextual) {
        if(isset($this->idStore[$contextual])) {
            return $this->idStore[$contextual];
        } else {
            $id = 'PHPCDI\Context\ApplicationContextImpl#' . $this->idCounter++;
            $this->idStore[$contextual] = $id;
            return $id;
        }
    }
}
