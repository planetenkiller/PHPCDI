<?php

use Doctrine\Common\ClassLoader;
require '../../vendor/DoctrineCommon/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('PHPCDI', '../..');
$classLoader->register();
$classLoader = new ClassLoader('PHPCDI\Extensions\Doctrine2', '../../Extensions/Doctrine2/src');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common', '../../vendor/DoctrineCommon');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\DBAL', '../../Extensions/Doctrine2/vendor');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\ORM', '../../Extensions/Doctrine2/vendor');
$classLoader->register();
$classLoader = new ClassLoader('PHPCDI\Example\Doctrine', '.');
$classLoader->register();

$doctrineBundle = new PHPCDI\Bootstrap\FileScanClassBundle('doctrine2', '../../Extensions/Doctrine2/src', 'PHPCDI\Extensions\Doctrine2');
$classpathBundle = new PHPCDI\Bootstrap\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Doctrine');

// dependencies
$classpathBundle->addClassBundle($doctrineBundle); // allow $classpathBundle to access beans from $doctrineBundle
$doctrineBundle->addClassBundle($classpathBundle); // allow $doctrineBundle to access beans from $classpathBundle (required because it needs access to doctrine configuration beans)


$deployment = new \PHPCDI\Bootstrap\Deployment();
$deployment->addClassBundle($doctrineBundle);
$deployment->addClassBundle($classpathBundle);


$configuration = new PHPCDI\Bootstrap\Configuration($deployment);
$container = $configuration->buildContainer();
$mng = $container->getManager($classpathBundle);


$beans = $mng->getBeans('PHPCDI\Example\Doctrine\Main', array(\PHPCDI\API\Inject\DefaultObj::newInstance()));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Doctrine\Main', $ctx);

$obj->main();
