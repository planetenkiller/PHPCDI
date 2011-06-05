<?php

namespace PHPCDI\Event;

use PHPCDI\SPI\AnnotatedMethod;
use PHPCDI\SPI\Bean;
use PHPCDI\SPI\ObserverMethod;
use PHPCDI\Manager\BeanManager;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\Beans as BeanUtil;
use PHPCDI\API\Annotations;
use PHPCDI\API\DefinitionException;

/**
 * Default implementation of ObserverMethod;
 */
class ObserverMethodImpl implements ObserverMethod {
    
    /**
     * @var \PHPCDI\SPI\Bean
     */
    private $declaringBean;
    
    /**
     *
     * @var \PHPCDI\SPI\AnnotatedMethod
     */
    private $method;
    private $qualifiers;
    private $type;
    private $reception;
    private $typeFilter;
    
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    public function __construct(Bean $declaringBean, AnnotatedMethod $method, BeanManager $beanManager) {
        $this->declaringBean = $declaringBean;
        $this->beanManager = $beanManager;
        $this->method = $method;
        
        $params = $method->getParameters();
        $param = $params[0];
        
        $this->qualifiers = AnnotationUtil::getQualifiers($param);
        $this->type = $param->getBaseType();
        
        if($param->isAnnotationPresent(Annotations\TypeFilter::className())) {
            $this->typeFilter = $param->getAnnotation(Annotations\TypeFilter::className())->value;
            
            if(empty($this->typeFilter)) {
                throw new DefinitionException('TypeFilter annotation must have an value in ' . $method->getBaseType() . '::' . $method->getPHPMember()->name);
            }
        }
        
        $observesAnnotation = $param->getAnnotation(Annotations\Observes::className());
        if($observesAnnotation == null) {
            throw new DefinitionException('First parameter of an observer method must have the @Observes annotation: [' . $method . ']');
        }
        
        if(!empty($observesAnnotation->value)) {
            if($observesAnnotation->value == 'ifExits') {
                $this->reception = self::RECEPTION_IF_EXISTS;
            } else if($observesAnnotation->value == 'always') {
                $this->reception = self::RECEPTION_ALWAYS;
            } else {
                throw new DefinitionException('Invalid value for @Observes annotation value [' . $method . ']:' . $observesAnnotation->value);
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
    
    public function getObservedTypeFilter() {
        return $this->typeFilter;
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
        $injectionPoints = BeanUtil::getParameterInjectionPoints($this->declaringBean, $this->method);
        unset($injectionPoints[0]);// first injection point is the parameter with @Disposes

        $values = array($eventData);
        foreach ($injectionPoints as $injection) {
            $values[] = $this->beanManager->getInjectableReference($injection, $ctx);
        }
        
        $this->method->getPHPMember()->invokeArgs($obj, $values);
        
        if($ctx != null && $this->declaringBean->getScope() instanceof Annotations\Dependent) {
            $ctx->release();
        }
    }
}
