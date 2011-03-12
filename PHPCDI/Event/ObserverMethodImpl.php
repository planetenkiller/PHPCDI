<?php

namespace PHPCDI\Event;

use PHPCDI\API\Inject\SPI\AnnotatedMethod;
use PHPCDI\API\Inject\SPI\Bean;

/**
 * Default implementation of ObserverMethod;
 */
class ObserverMethodImpl implements \PHPCDI\API\Inject\SPI\ObserverMethod {
    
    /**
     * @var \PHPCDI\API\Inject\SPI\Bean
     */
    private $declaringBean;
    
    /**
     *
     * @var \PHPCDI\API\Inject\SPI\AnnotatedMethod
     */
    private $method;
    private $qualifiers;
    private $type;
    private $reception;
    
    /**
     * @var \PHPCDI\API\Inject\SPI\BeanManager
     */
    private $beanManager;
    
    public function __construct(Bean $declaringBean, AnnotatedMethod $method, \PHPCDI\API\Inject\SPI\BeanManager $beanManager) {
        $this->declaringBean = $declaringBean;
        $this->beanManager = $beanManager;
        $this->method = $method;
        
        $params = $method->getParameters();
        $param = $params[0];
        
        $this->qualifiers = \PHPCDI\Util\Annotations::getQualifiers($param);
        $this->type = $param->getBaseType();
        
        $observesAnnotation = $param->getAnnotation('PHPCDI\API\Inject\Observes');
        if($observesAnnotation == null) {
            throw new \PHPCDI\API\Inject\DefinitionException('First parameter of an observer method must have the @Observes annotation: [' . $method . ']');
        }
        
        if(!empty($observesAnnotation->value)) {
            if($observesAnnotation->value == 'ifExits') {
                $this->reception = self::RECEPTION_IF_EXISTS;
            } else if($observesAnnotation->value == 'always') {
                $this->reception = self::RECEPTION_ALWAYS;
            } else {
                throw new \PHPCDI\API\Inject\DefinitionException('Invalid value for @Observes annotation value [' . $method . ']:' . $observesAnnotation->value);
            }
        }
    }

    public function getBeanClass() {
        return $this->declaringBean->getBeanClass();
    }

    public function getObservedQualifiers() {
        return $this->qualifiers;
    }

    public function getObservedType() {
        return $this->type;
    }

    public function getReception() {
        return $this->reception;
    }

    public function notify($eventData) {
        if($this->method->isStatic()) {
            $this->sendEvent($eventData, null, $this->beanManager->createCreationalContext($this->declaringBean));
        } else if($this->reception == self::RECEPTION_IF_EXISTS) {
            $obj = $this->beanManager->getRefernce($this->declaringBean, $this->getBeanClass(), null);
            
            if($obj != null) {
                $this->sendEvent($eventData, $obj, null);
            } 
        } else {
            $ctx = $this->beanManager->createCreationalContext($this->declaringBean);
            $obj = $this->beanManager->getRefernce($this->declaringBean, $this->getBeanClass(), $ctx);
            $this->sendEvent($eventData, $obj, $ctx);
        }
    }
    
    private function sendEvent($eventData, $obj, $ctx) {
        $injectionPoints = \PHPCDI\Util\Beans::getParameterInjectionPoints($this->declaringBean, $this->method);
        unset($injectionPoints[0]);// first injection point is the parameter with @Disposes

        $values = array($eventData);
        foreach ($injectionPoints as $injection) {
            $values[] = $this->beanManager->getInjectableReference($injection, $ctx);
        }
        
        $this->method->getPHPMember()->invokeArgs($obj, $values);
        
        if($ctx != null && $this->declaringBean->getScope() instanceof \PHPCDI\API\Inject\Dependent) {
            $ctx->release();
        }
    }
}
