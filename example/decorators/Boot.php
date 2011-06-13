<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
use PHPCDI\API\Annotations;

require __DIR__.'/../../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespace('PHPCDI', __DIR__ . '/../../');
$classLoader->registerNamespace('Doctrine\Common',  __DIR__ . '/../../vendor/doctrine_common/lib');
$classLoader->registerNamespace('PHPCDI\Example\Decorators', __DIR__);
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