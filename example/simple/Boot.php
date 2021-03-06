<?php

//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

use Symfony\Component\ClassLoader\UniversalClassLoader;
use PHPCDI\API\Annotations;

require __DIR__.'/../../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespace('PHPCDI', __DIR__ . '/../../');
$classLoader->registerNamespace('Doctrine\Common',  __DIR__ . '/../../vendor/doctrine_common/lib');
$classLoader->registerNamespace('PHPCDI\Example\Simple', __DIR__);
$classLoader->register();

$deployment = new PHPCDI\SPI\Bootstrap\Impl\Deployment();
$classBundle = new PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('classpath', '.', 'PHPCDI\Example\Simple');
$deployment->addClassBundle($classBundle);
$configuration = new PHPCDI\API\Configuration($deployment);


$container = $configuration->buildContainer();
$mng = $container->getManager($classBundle);


$beans = $mng->getBeans('PHPCDI\Example\Simple\Main', array(Annotations\DefaultObj::className(), Annotations\Any::className()));
$bean = $mng->resolve($beans);
$ctx = $mng->createCreationalContext($bean);
$obj = $mng->getRefernce($bean, 'PHPCDI\Example\Simple\Main', $ctx);
$ctx2 = $mng->createCreationalContext($bean);
$obj2 = $mng->getRefernce($bean, 'PHPCDI\Example\Simple\Main', $ctx2);


$obj->testStore1();
$obj->testStore2();

echo 'same: '.($obj === $obj2?'true':'false');


echo "\n\n mem_peek kb " . (memory_get_peak_usage(true) / 1024);
echo "\n mem_usage kb ". (memory_get_usage(true) / 1024);

//
//$xhprof_data = xhprof_disable();
//
//include_once "/opt/lampp/htdocs/prj/xhprof/xhprof_lib/utils/xhprof_lib.php";
//include_once "/opt/lampp/htdocs/prj/xhprof/xhprof_lib/utils/xhprof_runs.php";
//
//$xhprof_runs = new XHProfRuns_Default();
//
//// Save the run under a namespace "xhprof_foo".
////
//// **NOTE**:
//// By default save_run() will automatically generate a unique
//// run id for you. [You can override that behavior by passing
//// a run id (optional arg) to the save_run() method instead.]
////
//$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_phpcdi_opt1");
//
//echo "---------------\n".
//     "Assuming you have set up the http based UI for \n".
//     "XHProf at some address, you can view run at \n".
//     "http://localhost/prj/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_foo\n".
//     "---------------\n";
