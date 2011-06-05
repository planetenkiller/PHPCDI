<?php

namespace PHPCDI\Resolution;

use PHPCDI\API\Annotations;
use PHPCDI\Util\Beans as BeanUtil;

/**
 * A bean reslover based on types/qualifiers of the beans.
 */
class TypeSafeBeanReslover implements Resolver {

    private $beans;

    /**
     * @param \Traversable $beans
     */
    public function __construct($beans) {
        $this->beans = $beans;
    }

    public function reslove($beanType, $qualifiers) {
        if($beanType == 'PHPCDI\API\Instance' 
                || $beanType == 'PHPCDI\API\Event') {
            $qualifiers = array(Annotations\Any::className());
        }
        
        $beans = array();

        foreach($this->beans as $bean) {
            if(\in_array($beanType, $bean->getTypes())) {
                if(!BeanUtil::compareQualifiers($bean->getQualifiers(), $qualifiers)) {
                    continue;
                }

                $beans[] = $bean;
            }
        }

        return $beans;
    }
}
