<?php

namespace PHPCDI\Resolution;

/**
 * A reslover based on types of the beans.
 */
class TypeSafeReslover implements Resolver {

    private $beans;

    /**
     * @param \Traversable $beans
     */
    public function __construct($beans) {
        $this->beans = $beans;
    }

    public function reslove($beanType, $qualifiers) {
        $beans = array();

        foreach($this->beans as $bean) {
            if(\in_array($beanType, $bean->getTypes())) {
                foreach($qualifiers as $qualifier) {
                    foreach($bean->getQualifiers() as $anno) {
                        $qualifierClass = \is_object($qualifier)? \get_class($qualifier) : $qualifier;
                        if($qualifierClass == \get_class($anno)) { //TODO check anno prop too
                            continue 2;
                        }
                    }

                    continue 2;
                }

                $beans[] = $bean;
            }
        }

        return $beans;
    }
}
