<?php

namespace PHPCDI\Resolution;

/**
 * A ObserverMethod reslover based on types/qualifiers of the ObserverMethods.
 */
class TypeSafeObserverReslover implements Resolver {

    private $observers;

    /**
     * @param \Traversable $beans
     */
    public function __construct($observers) {
        $this->observers = $observers;
    }

    public function reslove($beanType, $qualifiers) {
        $observers = array();

        foreach($this->observers as $observer) {
            if(\in_array($observer->getObservedType(), \PHPCDI\Util\ReflectionUtil::getClassNames(new \ReflectionClass($beanType)))) {
                if(!\PHPCDI\Util\Beans::containsAllQualifiers($observer->getObservedQualifiers(), $qualifiers)) {
                    continue;
                }

                $observers[] = $observer;
            }
        }

        return $observers;
    }
}
