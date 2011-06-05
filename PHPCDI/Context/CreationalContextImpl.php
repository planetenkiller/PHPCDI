<?php

namespace PHPCDI\Context;

use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\SPI\Context\Contextual;

/**
 * CreationalContext implementation
 */
class CreationalContextImpl implements CreationalContext {

    /**
     * @var \PHPCDI\SPI\Context\Contextual
     */
    private $contextual;

    /**
     * @var \SplObjectStorage
     */
    private $incompleteInstances;

    /**
     * @var \ArrayObject
     */
    private $dependentInstances;

    /**
     * @var \ArrayObject
     */
    private $parentDependentInstances;

    /**
     * @param \PHPCDI\SPI\Context\Contextual $contextual
     */
    public function __construct(Contextual $contextual, \ArrayObject $parentDependentInstances=null) {
        $this->contextual = $contextual;
        $this->incompleteInstances = new \SplObjectStorage();
        $this->dependentInstances = new \ArrayObject(array());
        
        if($parentDependentInstances == null) {
            $this->parentDependentInstances = new \ArrayObject(array());
        } else {
            $this->parentDependentInstances = $parentDependentInstances;
        }
    }


    public function push($incompleteInstance) {
        $this->incompleteInstances[$this->contextual] = $incompleteInstance;
    }

    public function release() {
        foreach($this->dependentInstances as $instance) {
            $instance->getContextual()->destroy($instance->getInstance(), $instance->getCreationalContext());
        }

        foreach ($this->incompleteInstances as $key => $value) {
            unset($this->incompleteInstances[$key]);
        }
    }

    public function getCreationalContext(Contextual $contextual) {
        return new CreationalContextImpl($contextual, $this->dependentInstances);
    }

    public function addDependentInstance(ContextualInstance $instance) {
        $this->parentDependentInstances->append($instance);
    }
}
