<?php

namespace PHPCDI\API;

class DefinitionException extends \Exception {
    
    public function __construct($message) {
        parent::__construct($message);
    }
    
    public static function fromExceptionList(array $exceptions) {
        $message = "definition exceptions: \n";
        
        foreach($exceptions as $e) {
            $message .= $e . "\n\n";
        }
        
        return new DefinitionException($message);
    }
}

