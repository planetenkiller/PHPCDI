<?php

namespace PHPCDI\Event;

use PHPCDI\API\Event;
use PHPCDI\Manager\BeanManager;
use PHPCDI\SPI\InjectionPoint;
use PHPCDI\API\Annotations;
use PHPCDI\API\DefinitionException;
use PHPCDI\Injection\EventFacadeInjectionPoint;

/**
 * Default implementation of event interface.
 */
class EventImpl implements Event {
    
    /**
     * @var \PHPCDI\SPI\InjectionPoint
     */
    private $injectionPoint;
    
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    /**
     * @var string 
     */
    private $eventDataType;
    
    public function __construct(InjectionPoint $ij, BeanManager $beanManager) {
        $this->injectionPoint = $ij;
        $this->beanManager = $beanManager;
        
        $eventAnnotation = $ij->getAnnotated()->getAnnotation(Annotations\Event::className());
        if($eventAnnotation == null || empty($eventAnnotation->value)) {
            throw new DefinitionException('event injection point [' . $ij . '] must declare its event data type with a @Event annotation');
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
        return new EventImpl(new EventFacadeInjectionPoint($this->injectionPoint, $qualifiers), 
                             $this->beanManager);
    }
}

