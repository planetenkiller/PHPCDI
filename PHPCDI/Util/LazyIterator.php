<?php

namespace PHPCDI\Util;

class LazyIterator implements \IteratorAggregate {
    private $iterator;

    /**
     * @param callback $iterator
     */
    public function __construct($iterator) {
        $this->iterator = $iterator;
    }

    public function getIterator() {
        $it = $this->iterator;
        return $it();
    }
}

