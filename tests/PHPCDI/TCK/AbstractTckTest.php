<?php

namespace PHPCDI\TCK;

use Doctrine\Common\ClassLoader;

$classLoader = new ClassLoader('PHPCDI\TCK',  __DIR__ . '/../../');
$classLoader->register();

abstract class AbstractTckTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PHPCDI\Container 
     */
    private $container;
    
    /**
     * @var PHPCDI\API\Inject\SPI\BeanManager
     */
    private $manager;
    private $testClassNamespace;
    
    protected function setUp() {
        $cls = new \ReflectionClass($this);
        $namespace = $cls->getNamespaceName();
        $this->testClassNamespace = $namespace . '\\';
        
        $path = __DIR__ . '/../../';
        
        $deployment = new \PHPCDI\Bootstrap\Deployment();
        $classBundle = new \PHPCDI\Bootstrap\FileScanClassBundle('classpath', $path, $namespace);
        $deployment->addClassBundle($classBundle);
        $configuration = new \PHPCDI\Bootstrap\Configuration($deployment);


        $this->container = $configuration->buildContainer();
        $this->manager = $this->container->getManager($classBundle);
    }
    
    /**
     * @param string $class relative class name
     * 
     * @return array
     */
    protected function getBeans($class, $qulaifiers=array()) {
        return $this->manager->getBeans($this->testClassNamespace . $class, $qulaifiers);
    }
    
    /**
     * @param string $class relative class name
     * 
     * @return array
     */
    protected function getBean($class) {
        $list = $this->manager->getBeans($this->testClassNamespace . $class, array());
        
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
     * @return PHPCDI\API\Inject\SPI\BeanManager
     */
    protected function getManager() {
        return $this->manager;
    }
}

