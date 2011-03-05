<?php

namespace PHPCDI\Example\Simple;

class ArrayStore implements Store {
    private $data;
    
    public function __construct() {
        $this->data = array();
    }

    public function get($var) {
        return isset($this->data[$var]) ? $this->data[$var] : null;
    }

    public function put($var, $value) {
        $this->data[$var] = $value;
    }
}

