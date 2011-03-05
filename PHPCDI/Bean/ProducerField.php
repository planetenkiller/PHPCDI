<?php

namespace PHPCDI\Bean;

use PHPCDI\API\Inject\SPI\AnnotatedField;
use PHPCDI\API\Inject\SPI\Bean;

class ProducerField extends AbstractProducer {

    /**
     * @var BeanManager
     */
    private $beanManager;

    public function __construct(Bean $declaringBean, AnnotatedField $field, BeanManager $beanManager) {
        parent::__construct($declaringBean, $field, array());
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
        $declaringBeanObj = $this->beanManager->getRefernce($this->declaringBean, $this->member->getBaseType(), $creationalContext);
        $reflectionProperty = $this->member->getPHPMember();
        $reflectionProperty->setAccessible(true);

        $obj = $reflectionMethod->getValue($declaringBeanObj);

        if($this->getScope()instanceof \PHPCDI\API\Inject\Dependent) {
            $creationalContext->release();
        }

        return $obj;
    }

    public function destroy($instance, $creationalContext) {
        //todo: invoke disposer method
    }
}
