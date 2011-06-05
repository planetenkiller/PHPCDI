<?php

namespace PHPCDI\API;


interface Event {
    public function fire($eventData);
    
    /**
     * @return Event
     */
    public function select(array $qualifiers);
    
    /**
     * @return Event
     */
    public function selectEvent($eventDataClassname, array $qualifiers);
}

