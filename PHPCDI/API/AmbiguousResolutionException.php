<?php

namespace PHPCDI\API;

class AmbiguousResolutionException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

