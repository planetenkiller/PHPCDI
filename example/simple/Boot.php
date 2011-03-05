<?php

use Doctrine\Common\ClassLoader;
require '../../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('PHPCDI', '../..');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common', '../../vendor/DoctrineCommon');
$classLoader->register();
$classLoader = new ClassLoader('PHPCDI\Example\Simple', '.');
$classLoader->register();

$deployment = new \PHPCDI\Bootstrap\Deployment();
$classBundle = new PHPCDI\Bootstrap\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Simple');
$deployment->addClassBundle($classBundle);
$configuration = new PHPCDI\Bootstrap\Configuration($deployment);


$container = $configuration->buildContainer();
$mng = $container->getManager($classBundle);


$beans = $mng->getBeans('PHPCDI\Example\Simple\Main', array('PHPCDI\API\Inject\DefaultObj', 'PHPCDI\API\Inject\Any'));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Simple\Main', $ctx);
$ctx2 = $mng->createCreationalContext($bean);
$obj2 = $mng->getRefernce($bean, 'PHPCDI\Example\Simple\Main', $ctx2);


$obj->testStore1();
$obj->testStore2();

echo 'same: '.($obj === $obj2?'true':'false');




