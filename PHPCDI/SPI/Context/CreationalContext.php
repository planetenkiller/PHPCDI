<?php

namespace PHPCDI\SPI\Context;

interface CreationalContext {
    public function push($incompleteInstance);
    public function release();
}
