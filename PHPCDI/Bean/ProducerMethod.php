<?php

namespace PHPCDI\Bean;

use PHPCDI\API\Inject\SPI\AnnotatedMethod;
use PHPCDI\API\Inject\SPI\Bean;

class ProducerMethod extends AbstractProducer {

    /**
     * @var BeanManager
     */
    private $beanManager;

    public function __construct(Bean $declaringBean, AnnotatedMethod $method, BeanManager $beanManager) {
        $ij = \PHPCDI\Util\Beans::getParameterInjectionPoints($declaringBean, $method);
        parent::__construct($declaringBean, $method, $ij);
        $this->beanManager = $beanManager;
    }

    public function create($creationalContext) {
        $declaringBeanObj = $this->beanManager->getRefernce($this->declaringBean, $this->member->getBaseType(), $creationalContext);
        $reflectionMethod = $this->member->getPHPMember();

        $values = array();
        foreach ($this->getInjectionPoints() as $injection) {
            $values[] = $mgr->getInjectableReference($injection, $ctx);
        }

        if(\count($values) > 0) {
            $obj = $reflectionMethod->invokeArgs($declaringBeanObj, $values);
        } else {
            $obj = $reflectionMethod->invoke($declaringBeanObj);
        }

        if($this->getScope()instanceof \PHPCDI\API\Inject\Dependent) {
            $creationalContext->release();
        }

        return $obj;
    }

    public function destroy($instance, $creationalContext) {
        //todo: invoke disposer method
    }
}
