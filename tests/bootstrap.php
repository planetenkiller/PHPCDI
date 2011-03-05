<?php

use Doctrine\Common\ClassLoader;
require __DIR__.'/../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('PHPCDI',  __DIR__ . '/../');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common',  __DIR__ . '/../vendor/DoctrineCommon');
$classLoader->register();