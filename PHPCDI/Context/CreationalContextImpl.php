<?php

namespace PHPCDI\Context;

/**
 * CreationalContext implementation
 */
class CreationalContextImpl implements \PHPCDI\API\Context\SPI\CreationalContext {

    /**
     * @var \PHPCDI\API\Context\SPI\Contextual
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
     * @param \PHPCDI\API\Context\SPI\Contextual $contextual
     */
    public function __construct($contextual, \ArrayObject $parentDependentInstances=null) {
        $this->contextual= $contextual;
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

    public function getCreationalContext(\PHPCDI\API\Context\SPI\Contextual $contextual) {
        return new CreationalContextImpl($contextual, $this->dependentInstances);
    }

    public function addDependentInstance(ContextualInstance $instance) {
        $this->parentDependentInstances->append($instance);
    }
}
