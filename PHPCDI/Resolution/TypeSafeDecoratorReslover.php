<?php

namespace PHPCDI\Resolution;

use PHPCDI\Util\Beans as BeanUtil;

/**
 * A Decorator reslover based on types/qualifiers of the Decorators.
 */
class TypeSafeDecoratorReslover implements Resolver {

    private $decorators;

    /**
     * @param \Traversable $decorators
     */
    public function __construct($decorators) {
        $this->decorators = $decorators;
    }

    public function reslove($beanType, $qualifiers) {
        $decorators = array();

        foreach($this->decorators as $decorator) {
            if(\in_array($decorator->getDelegateType(), $beanType)) {
                if(!BeanUtil::containsAllQualifiers($decorator->getDelegateQualifiers(), $qualifiers)) {
                    continue;
                }

                $decorators[] = $decorator;
            }
        }

        return $decorators;
    }
}
