<?php

namespace PHPCDI\API\Event;


interface Event {
    public function fire($eventData);
    
    /**
     * @return PHPCDI\API\Event\Event
     */
    public function select(array $qualifiers);
    
    /**
     * @return PHPCDI\API\Event\Event
     */
    public function selectEvent($eventDataClassname, array $qualifiers);
}

