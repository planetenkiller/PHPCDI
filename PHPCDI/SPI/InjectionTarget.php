<?php

namespace PHPCDI\SPI;

use PHPCDI\SPI\Context\CreationalContext;

/**
 *
 */
interface InjectionTarget extends Producer {
    public function inject($instance, CreationalContext $creationalContext);
    public function postConstruct($instance);
    public function preDestory($instance);
}
