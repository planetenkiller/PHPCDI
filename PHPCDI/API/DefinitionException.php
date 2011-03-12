<?php

namespace PHPCDI\API;

class DefinitionException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

