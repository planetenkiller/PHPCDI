<?php

namespace PHPCDI\Bootstrap;

use PHPCDI\API\Bootstrap\ClassBundle;
use PHPCDI\Bean\BeanManager;

/**
 * PHPCDI configruation builder to configure an new container instance.
 */
class Configuration {
    /**
     * @var PHPCDI\API\Bootstrap\Deployment
     */
    private $deployment;
    private $contexts;

    public function __construct(\PHPCDI\API\Bootstrap\Deployment $deployment) {
        $this->deployment = $deployment;
        $this->contexts = array();
        $this->addDefaultObjects();
    }

    private function addDefaultObjects() {
        $this->addContext(new \PHPCDI\Context\ApplicationContextImpl())
             ->addContext(new \PHPCDI\Context\DependentContextImpl());
    }

    /**
     * @param \PHPCDI\API\Context\SPI\Context $context new context
     * 
     * @return Configuration returns this
     */
    public function addContext(\PHPCDI\API\Context\SPI\Context $context) {
        $this->contexts[] = $context;
        return $this;
    }

    /**
     * Builds and returns a new fully configured container.
     * 
     * @return \PHPCDI\Container
     */
    public function buildContainer() {
        $classBundleManager = new \SplObjectStorage();
        $rootManager = $this->buildManager($classBundleManager);

        return new \PHPCDI\Container($this->deployment, $rootManager, $classBundleManager);
    }

    private function buildManager(\SplObjectStorage $classBundleManager) {
        $manager = new \PHPCDI\Bean\BeanManager();
        foreach($this->contexts as $context) {
            $manager->addContext($context);
        }

        // create managers
        $stack = array();
        foreach($this->deployment->getClassBundles() as $classBundle) {
            $this->createManagerAndDeploy($classBundle, $manager, $classBundleManager, $stack);
        }

        // build beans and add them to the managers
        foreach($classBundleManager as $classBundle) {
            $manager = $classBundleManager[$classBundle];
            foreach($classBundle->getClasses() as $class) {
                $reflectionClass = new \ReflectionClass($class);
                if(!$reflectionClass->isAbstract() && !$reflectionClass->isInterface()) {
                    $type = new \PHPCDI\Introspector\AnnotatedTypeImpl($class);
                    $bean = new \PHPCDI\Bean\ManagedBean($class, $type, $manager);
                    $manager->addBean($bean);
                    $this->createProducer($bean, $type, $manager);
                }
            }
        }

        return $manager;
    }

    private function createManagerAndDeploy(ClassBundle $classBundle, BeanManager $rootManager, \SplObjectStorage $classBundleManager, array &$stack) {
        if(isset($classBundleManager[$classBundle])) {
            $parent = $classBundleManager[$classBundle];
        } else {
            $parent = new BeanManager($rootManager->getContexts());
            $classBundleManager[$classBundle] = $parent;
        }

        $stack[] = $classBundle;

        foreach($classBundle->getClassBundles() as $subClassBundle) {
            // break circular references
            if(!\in_array($subClassBundle, $stack)) {
                $parent->addAccessibleBeanManager($this->createManagerAndDeploy($subClassBundle, $rootManager, $classBundleManager, $stack));
            }
        }

        return $parent;
    }

    private function createProducer($declaringBean, \PHPCDI\Introspector\AnnotatedTypeImpl $class, BeanManager $manager) {
        foreach($class->getMethodsWithAnnotation('PHPCDI\API\Inject\Produces') as $method) {
            $manager->addBean(new \PHPCDI\Bean\ProducerMethod($declaringBean, $method, $manager));
        }
    }
}
