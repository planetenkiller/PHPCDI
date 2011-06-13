<?php

namespace PHPCDI\TCK;

use Symfony\Component\ClassLoader\UniversalClassLoader;

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespace('PHPCDI\TCK',  __DIR__ . '/../../');
$classLoader->register();

abstract class AbstractTckTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PHPCDI\API\Container 
     */
    private $container;
    
    /**
     * @var PHPCDI\SPI\BeanManager
     */
    private $manager;
    private $testClassNamespace;
    
    protected function setUp() {
        $cls = new \ReflectionClass($this);
        $namespace = $cls->getNamespaceName();
        $this->testClassNamespace = $namespace . '\\';
        
        $path = __DIR__ . '/../../';
        
        $deployment = new \PHPCDI\SPI\Bootstrap\Impl\Deployment();
        $classBundle = new \PHPCDI\SPI\Bootstrap\Impl\FileScanClassBundle('classpath', $path, $namespace);
        $deployment->addClassBundle($classBundle);
        $configuration = new \PHPCDI\API\Configuration($deployment);


        $this->container = $configuration->buildContainer();
        $this->manager = $this->container->getManager($classBundle);
    }
    
    /**
     * @param string $class relative class name
     * 
     * @return array
     */
    protected function getBeans($class, $qulaifiers=array(), $prefixWithNamespace=true) {
        return $this->manager->getBeans(
                ($prefixWithNamespace)? $this->testClassNamespace . $class : $class, 
                $qulaifiers);
    }
    
    /**
     * @param string $class relative class name
     * 
     * @return array
     */
    protected function getBean($class, $qulaifiers=array(), $prefixWithNamespace=true) {
        $list = $this->getBeans($class, $qulaifiers, $prefixWithNamespace);
        
        return $list[0];
    }
    
    /**
     * @param string $class relative class name
     * 
     * @return mixed
     */
    protected function getInstanceViaContext($class) {
        $bean = $this->getBean($class);
        
        return $this->manager->getContext($bean->getScope())->get($bean, $this->manager->createCreationalContext($bean));
        
    }
    
    /**
     * @return PHPCDI\SPI\BeanManager
     */
    protected function getManager() {
        return $this->manager;
    }
}

