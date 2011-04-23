<?php

use Doctrine\Common\ClassLoader;
require __DIR__.'/../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';
require __DIR__.'/PHPCDI/TCK/AbstractTckTest.php';

$classLoader = new ClassLoader('PHPCDI',  __DIR__ . '/../');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common',  __DIR__ . '/../vendor/DoctrineCommon');
$classLoader->register();