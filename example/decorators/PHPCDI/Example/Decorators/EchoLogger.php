<?php

namespace PHPCDI\Example\Decorators;

class EchoLogger implements Logger {
    public function log($msg, $ex) {
        echo $msg;
        echo $ex;
    }
}

