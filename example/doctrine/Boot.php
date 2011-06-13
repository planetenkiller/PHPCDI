<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
use PHPCDI\API\Annotations;

require __DIR__.'/../../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespace('PHPCDI', __DIR__ . '/../../');
$classLoader->registerNamespace('PHPCDI\Extensions\Doctrine2', __DIR__ . '/../../Extensions/Doctrine2/src');
$classLoader->registerNamespace('Doctrine\Common',  __DIR__ . '/../../vendor/doctrine_common/lib');
$classLoader->registerNamespace('Doctrine\DBAL', __DIR__ . '/../../Extensions/Doctrine2/vendor/doctrine2/lib/vendor/doctrine-dbal/lib');
$classLoader->registerNamespace('Doctrine\ORM', __DIR__ . '/../../Extensions/Doctrine2/vendor/doctrine2/lib');
$classLoader->registerNamespace('PHPCDI\Example\Doctrine', __DIR__);
$classLoader->register();


$doctrineBundle = new PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('doctrine2', '../../Extensions/Doctrine2/src', 'PHPCDI\Extensions\Doctrine2');
$classpathBundle = new PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Doctrine');

// dependencies
$classpathBundle->addClassBundle($doctrineBundle); // allow $classpathBundle to access beans from $doctrineBundle
$doctrineBundle->addClassBundle($classpathBundle); // allow $doctrineBundle to access beans from $classpathBundle (required because it needs access to doctrine configuration beans)


$deployment = new PHPCDI\SPI\Bootstrap\Impl\Deployment();
$deployment->addClassBundle($doctrineBundle);
$deployment->addClassBundle($classpathBundle);
$deployment->markAsExtension('PHPCDI\Extensions\Doctrine2\DoctrineExtension');


$configuration = new PHPCDI\API\Configuration($deployment);
$container = $configuration->buildContainer();
$mng = $container->getManager($classpathBundle);


$beans = $mng->getBeans('PHPCDI\Example\Doctrine\Main', array(Annotations\DefaultObj::newInstance()));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Doctrine\Main', $ctx);

$obj->main();
