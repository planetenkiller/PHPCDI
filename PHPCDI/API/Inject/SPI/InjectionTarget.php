<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface InjectionTarget extends Producer {
    public function inject($instance, $creationalContext);
    public function postConstruct($instance);
    public function preDestory($instance);
}
