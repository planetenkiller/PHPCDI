<?php

use Doctrine\Common\ClassLoader;
use PHPCDI\API\Annotations;

require '../../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('PHPCDI', '../..');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common', '../../vendor/DoctrineCommon');
$classLoader->register();
$classLoader = new ClassLoader('PHPCDI\Example\Decorators', '.');
$classLoader->register();

$deployment = new PHPCDI\SPI\Bootstrap\Impl\Deployment();
$classBundle = new PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Decorators');
$deployment->addClassBundle($classBundle);
$configuration = new PHPCDI\API\Configuration($deployment);


$container = $configuration->buildContainer();
$mng = $container->getManager($classBundle);


$beans = $mng->getBeans('PHPCDI\Example\Decorators\Main', array(Annotations\DefaultObj::className(), Annotations\Any::className()));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Decorators\Main', $ctx);


$obj->main();