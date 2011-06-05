<?php

namespace PHPCDI\SPI;

/**
 *
 */
interface InjectionPoint {
    public function getType();
    public function getQualifiers();
    /**
     * @return Bean
     */
    public function getBean();
    public function getMember();
    public function getAnnotated();
    public function isDelegate();
    public function isTransient();
}

