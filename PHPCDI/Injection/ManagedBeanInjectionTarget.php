<?php

namespace PHPCDI\Injection;

use PHPCDI\API\Inject\SPI\InjectionTarget;

/**
 * Injection target for managed beans.
 */
class ManagedBeanInjectionTarget implements InjectionTarget {
    /**
     * @var \PHPCDI\Bean\ManagedBean
     */
    private $bean;

    public function __construct(\PHPCDI\Bean\ManagedBean $bean) {
        $this->bean = $bean;
    }

    public function dispose($instance) {

    }
    public function getInjectionPoints() {

    }
    public function inject($instance, $creationalContext) {
        \PHPCDI\Util\Beans::injectFieldsAndInitializers(
                $instance,
                $creationalContext,
                $this->bean->getBeanManager(),
                $this->bean->getFieldInjectionPoints(),
                $this->bean->getInitializerMethods());
    }
    public function postConstruct($instance) {
        foreach($this->bean->getPostConstructMethods() as $method) {
            $method->getPHPMember()->invokeArgs($instance, array());
        }
    }
    public function preDestory($instance) {

    }

    /**
     * @param \PHPCDI\API\Context\SPI\CreationalContext $creationalContext
     */
    public function produce($creationalContext) {
        if(!$this->bean->hasDecorators()) {
            $instance = $this->bean->createInstance($creationalContext);
            $creationalContext->push($instance);
        } else {
            $obj = $this->bean->createInstance($creationalContext);
            $this->bean->applyDecorators($obj, $creationalContext, $this->bean->getBeanManager()->getCurrentInjectionPoint());
            return $obj;
        }
        return $instance;
    }
}
