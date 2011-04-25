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

        $container = new \PHPCDI\Container($this->deployment, $rootManager, $classBundleManager);
        
        $rootManager->fireEvent(new \PHPCDI\API\Event\ContainerInitialized(), array());
        
        return $container;
    }

    private function buildManager(\SplObjectStorage $classBundleManager) {
        $rootmanager = new \PHPCDI\Bean\BeanManager('root');
        foreach($this->contexts as $context) {
            $rootmanager->addContext($context);
        }

        // create managers
        foreach($this->deployment->getClassBundles() as $classBundle) {
            $this->createManagerAndDeploy($classBundle, $rootmanager, $classBundleManager, new \SplObjectStorage());
        }

        // build beans and add them to the managers
        foreach($classBundleManager as $classBundle) {
            $manager = $classBundleManager[$classBundle];
            foreach($classBundle->getClasses() as $class) {
                $reflectionClass = new \ReflectionClass($class);
                if(\PHPCDI\Util\ReflectionUtil::isManagedBean($reflectionClass)) {
                    $type = new \PHPCDI\Introspector\AnnotatedTypeImpl($class);
                    
                    if($type->isAnnotationPresent('PHPCDI\API\Inject\Decorator')) {
                        $decorator = new \PHPCDI\Bean\DecoratorImpl($class, $type, $manager);
                        $manager->addDecorator($decorator);
                    } else {
                        $bean = new \PHPCDI\Bean\ManagedBean($class, $type, $manager);
                        $manager->addBean($bean);
                        $this->createProducer($bean, $type, $manager);
                        $this->createObserver($bean, $type, $manager);
                    }
                }
            }
        }

        return $rootmanager;
    }

    private function createManagerAndDeploy(ClassBundle $classBundle, BeanManager $rootManager, \SplObjectStorage $classBundleManager, \SplObjectStorage $stack) {
        if(isset($classBundleManager[$classBundle])) {
            $parent = $classBundleManager[$classBundle];
        } else {
            $parent = new BeanManager($classBundle->getId(), $rootManager->getContexts(), $rootManager->getInjectionPointStack());
            $this->addBuiltinBeansToManager($parent);
            $classBundleManager[$classBundle] = $parent;
            $rootManager->addAccessibleBeanManager($parent);
        }

        $stack->attach($classBundle);

        foreach($classBundle->getClassBundles() as $subClassBundle) {
            // break circular references
            if(!$stack->contains($subClassBundle)) {
                $parent->addAccessibleBeanManager($this->createManagerAndDeploy($subClassBundle, $rootManager, $classBundleManager, $stack));
            }
        }

        return $parent;
    }

    private function createProducer($declaringBean, \PHPCDI\Introspector\AnnotatedTypeImpl $class, BeanManager $manager) {
        $disposers = $class->getMethodsWithAnnotationOnFirstParameter('PHPCDI\API\Inject\Disposes');
        
        foreach($class->getMethodsWithAnnotation('PHPCDI\API\Inject\Produces') as $method) {
            $disposer = null;
            
            foreach($disposers as $disposerMethod) {
                $params = $disposerMethod->getParameters();
                if(\in_array(\PHPCDI\Util\Annotations::getReturnType($method->getPHPMember()), $params[0]->getTypeClosure())
                   && \PHPCDI\Util\Beans::compareQualifiers(\PHPCDI\Util\Annotations::getQualifiers($params[0]), 
                                                            \PHPCDI\Util\Annotations::getQualifiers($method))) {
                    if($disposer == null) {
                        $disposer = $disposerMethod;
                    } else {
                        throw new \PHPCDI\API\Inject\DefinitionException('more than one disposer method for producer method '.$method->getPHPMember()->class.'::'.$method->getPHPMember()->name);
                    }
                }
            }
            
            
            $manager->addBean(new \PHPCDI\Bean\ProducerMethod($declaringBean, $method, $disposer, $manager));
        }
    }

    private function createObserver($declaringBean, \PHPCDI\Introspector\AnnotatedTypeImpl $class, BeanManager $manager) {
        $observers = $class->getMethodsWithAnnotationOnFirstParameter('PHPCDI\API\Inject\Observes');

        foreach($observers as $observer) {
            $manager->addObserver(new \PHPCDI\Event\ObserverMethodImpl($declaringBean, $observer, $manager));
        }
    }

    private function addBuiltinBeansToManager(BeanManager $beanManager) {
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\EventBean($beanManager));
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\InstanceBean($beanManager));
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\InjectionPointBean($beanManager));
    }
}
