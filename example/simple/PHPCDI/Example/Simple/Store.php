<?php

namespace PHPCDI\Example\Simple;


interface Store {
    public function put($var, $value);
    public function get($var);
}

