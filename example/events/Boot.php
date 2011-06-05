<?php

use Doctrine\Common\ClassLoader;
use PHPCDI\API\Annotations;

require '../../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('PHPCDI', '../..');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common', '../../vendor/DoctrineCommon');
$classLoader->register();
$classLoader = new ClassLoader('PHPCDI\Example\Events', '.');
$classLoader->register();

$deployment = new PHPCDI\SPI\Bootstrap\Impl\Deployment();
$classBundle = new PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Events');
$deployment->addClassBundle($classBundle);
$configuration = new PHPCDI\API\Configuration($deployment);


$container = $configuration->buildContainer();
$mng = $container->getManager($classBundle);


$beans = $mng->getBeans('PHPCDI\Example\Events\UserDao', array(Annotations\DefaultObj::className(), Annotations\Any::className()));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Events\UserDao', $ctx);

$user = new PHPCDI\Example\Events\User();
$user->setName('admin');
$user->setAdmin(true);

$obj->insert($user);
echo 'Admin user id: ' . $user->getId();
echo "\n-------------\n\n";

$user = new PHPCDI\Example\Events\User();
$user->setName('guest');

$obj->insert($user);
echo 'Guest user id: ' . $user->getId();




