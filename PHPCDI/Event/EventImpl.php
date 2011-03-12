<?php

namespace PHPCDI\Event;

use PHPCDI\API\Event\Event;
use PHPCDI\API\Inject\SPI\BeanManager;
use PHPCDI\API\Inject\SPI\InjectionPoint;

/**
 * Default implementation of event interface.
 */
class EventImpl implements Event {
    
    /**
     * @var \PHPCDI\API\Inject\SPI\InjectionPoint 
     */
    private $injectionPoint;
    
    /**
     * @var \PHPCDI\API\Inject\SPI\BeanManager
     */
    private $beanManager;
    
    /**
     * @var string 
     */
    private $eventDataType;
    
    public function __construct(InjectionPoint $ij, BeanManager $beanManager) {
        $this->injectionPoint = $ij;
        $this->beanManager = $beanManager;
        
        $eventAnnotation = $ij->getAnnotated()->getAnnotation('PHPCDI\API\Inject\Event');
        if($eventAnnotation == null || empty($eventAnnotation->value)) {
            throw new \PHPCDI\API\Inject\DefinitionException('event injection point [' . $ij . '] must declare its event data type with a @Event annotation');
        }
        $this->eventDataType = $eventAnnotation->value;
    }

    public function fire($eventData) {
        $this->beanManager->fireEvent($eventData, $this->injectionPoint->getQualifiers());
    }

    public function select(array $qualifiers) {
        return $this->selectEvent($this->eventDataType, $qualifiers);
    }

    public function selectEvent($eventDataClassname, array $qualifiers) {
        return new EventImpl(new \PHPCDI\Injection\EventFacadeInjectionPoint($this->injectionPoint, $qualifiers), 
                             $this->beanManager);
    }
}

