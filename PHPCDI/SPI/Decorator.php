<?php

namespace PHPCDI\SPI;


interface Decorator extends Bean {
    public function getDecoratedTypes();
    
    public function getDelegateType();
    
    public function getDelegateQualifiers();
}
