<?php

namespace PHPCDI\API\Inject;

class DefinitionException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

