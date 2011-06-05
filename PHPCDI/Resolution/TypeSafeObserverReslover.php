<?php

namespace PHPCDI\Resolution;

use PHPCDI\Util\Beans as BeanUtil;
use PHPCDI\Util\ReflectionUtil;

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
        $typeFilter = null;

        if(is_array($beanType)) {
            $typeFilter = $beanType[1];
            $beanType = $beanType[0];
        }
        
        foreach($this->observers as $observer) {
            if(\in_array($observer->getObservedType(), ReflectionUtil::getClassNames($beanType))) {
                if($typeFilter && !\in_array($observer->getObservedTypeFilter(), ReflectionUtil::getClassNames($typeFilter))) {
                    continue;
                }
                
                if(!BeanUtil::containsAllQualifiers($observer->getObservedQualifiers(), $qualifiers)) {
                    continue;
                }

                $observers[] = $observer;
            }
        }

        return $observers;
    }
}
