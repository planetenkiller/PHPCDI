<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
require __DIR__.'/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require __DIR__.'/PHPCDI/TCK/AbstractTckTest.php';

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespace('PHPCDI', __DIR__ . '/../');
$classLoader->registerNamespace('Doctrine\Common',  __DIR__ . '/../vendor/doctrine_common/lib');
$classLoader->register();