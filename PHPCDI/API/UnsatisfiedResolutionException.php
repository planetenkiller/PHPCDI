<?php

namespace PHPCDI\API;

class UnsatisfiedResolutionException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

