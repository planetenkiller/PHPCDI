<?php

namespace PHPCDI\Bean;

class DecoratorImpl extends ManagedBean implements \PHPCDI\API\Inject\SPI\Decorator {
    /**
     * @var \PHPCDI\API\Inject\SPI\InjectionPoint 
     */
    private $delegateInjectionPoint;
    
    private $decoratedTypes;
    
    public function __construct($className, \PHPCDI\API\Inject\SPI\AnnotatedType $annotatedType, \PHPCDI\Bean\BeanManager $beanManager) {
        parent::__construct($className, $annotatedType, $beanManager);
        
        $delegateInjectionPoints = array();
        foreach($this->getInjectionPoints() as $ij) {
            /* @var $ij \PHPCDI\API\Inject\SPI\InjectionPoint */
            if($ij->isDelegate()) {
                $delegateInjectionPoints[] = $ij;
            }
        }
        
        if(\count($delegateInjectionPoints) == 0) {
            throw new \PHPCDI\API\DefinitionException('Decorator ' . $className . ' must have an delegate injection point');
        } else if(\count($delegateInjectionPoints) > 1) {
            throw new \PHPCDI\API\DefinitionException('Decorator ' . $className . ' must have exactly one delegate injection point');
        } else {
            $this->delegateInjectionPoint = $delegateInjectionPoints[0];
        }
        
        $this->decoratedTypes = \class_implements($className);
    }

    public function getDecoratedTypes() {
        return $this->decoratedTypes;
    }

    public function getDelegateQualifiers() {
        return $this->delegateInjectionPoint->getQualifiers();
    }

    public function getDelegateType() {
        return $this->delegateInjectionPoint->getType();
    }
    
    protected function isProxyRequired() {
        return $this->annotatedType->getPHPClass()->isAbstract();
    }
    
    public function getDecorators() {
        return array();
    }
}

