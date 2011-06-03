<?php

namespace PHPCDI\Resolution;

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
        if($beanType == 'PHPCDI\API\Instance\Instance' 
                || $beanType == 'PHPCDI\API\Event\Event') {
            $qualifiers = array(\PHPCDI\API\Inject\Any::className());
        }
        
        $beans = array();

        foreach($this->beans as $bean) {
            if(\in_array($beanType, $bean->getTypes())) {
                if(!\PHPCDI\Util\Beans::compareQualifiers($bean->getQualifiers(), $qualifiers)) {
                    continue;
                }

                $beans[] = $bean;
            }
        }

        return $beans;
    }
}
