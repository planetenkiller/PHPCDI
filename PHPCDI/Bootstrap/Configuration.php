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
        // create bean manager graph
        $classBundleManager = new \SplObjectStorage();
        $rootManager = $this->buildManager($classBundleManager);
        
        // deploy extensions
        foreach($this->deployment->getExtensions() as $extensionClassName) {
            $classBundle = $this->deployment->getBundleOfClass($extensionClassName);
            $beanManager = $classBundleManager[$classBundle];
            
            $bean = new \PHPCDI\Bean\Builtin\ExtensionBean($beanManager, new $extensionClassName());
            $type = new \PHPCDI\Introspector\AnnotatedTypeImpl($extensionClassName);
            $beanManager->addBean($bean);
            $this->createObserver($bean, $type, $beanManager);
        }
        
        // BeforeBeanDiscovery event (used by extensions)
        $eventdata = new Event\BeforeBeanDiscoveryImpl();
        $rootManager->fireEvent($eventdata, array(\PHPCDI\API\Inject\Any::newInstance()));
        
        // create beans
        $this->buildBeans($classBundleManager);
        
        // AfterBeanDiscovery event
        $eventdata = new Event\AfterBeanDiscoveryImpl();
        $rootManager->fireEvent($eventdata, array(\PHPCDI\API\Inject\Any::newInstance()));
        
        if($eventdata->getErrors()) {
            throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
        }
        
        foreach($eventdata->getBeans() as $bean) {
            $beanClassName = $bean->getBeanClass();
            $classBundle = $this->deployment->getBundleOfClass($beanClassName);
            
            if($classBundle == null) {
                throw new \PHPCDI\API\DefinitionException('Bean of class ' . $beanClassName . ' has no class bundle (bean added via AfterBeanDiscovery event)');
            }
            
            // process bean event
            $eventdataSub = new Event\ProcessBeanImpl($bean, null);//TODO get annotated type somehow
            $classBundleManager[$classBundle]->fireEvent(array($eventdataSub, $bean->getBeanClass()), array(\PHPCDI\API\Inject\Any::newInstance()));

            if($eventdataSub->getErrors()) {
                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdataSub->getErrors());
            }
            
            if($bean instanceof \PHPCDI\API\Inject\SPI\Decorator) {
                $classBundleManager[$classBundle]->addDecorator($bean);
            } else {
                $classBundleManager[$classBundle]->addBean($bean);
            }
        }
        
        foreach ($eventdata->getObservers() as $observer) {
            $beanClassName = $bean->getBeanClass();
            $classBundle = $this->deployment->getBundleOfClass($beanClassName);
            
            if($classBundle == null) {
                throw new \PHPCDI\API\DefinitionException('Observer ' . $observer . ' has no class bundle (observer added via AfterBeanDiscovery event)');
            }
            
            $classBundleManager[$classBundle]->addObserver($observer);
        }
        
        foreach ($eventdata->getContexts() as $context) {
            $rootManager->addContext($context);
        }

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
    
    private function buildBeans(\SplObjectStorage $classBundleManager) {
        // build beans and add them to the managers
        foreach($classBundleManager as $classBundle) {
            $manager = $classBundleManager[$classBundle];
            foreach($classBundle->getClasses() as $class) {
                $reflectionClass = new \ReflectionClass($class);
               
                    $type = new \PHPCDI\Introspector\AnnotatedTypeImpl($class);
                    
                    // ProcessAnnotatedType event (used by extensions)
                    $eventdata = new Event\ProcessAnnotatedTypeImpl();
                    $eventdata->setAnnotatedType($type);
                    $manager->fireEvent(array($eventdata, $type->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                    $type = $eventdata->getAnnotatedType();
                    
                    if(!$eventdata->hasVeto() && \PHPCDI\Util\ReflectionUtil::isManagedBean($reflectionClass)) {
                        if($type->isAnnotationPresent('PHPCDI\API\Inject\Decorator')) {
                            $decorator = new \PHPCDI\Bean\DecoratorImpl($class, $type, $manager);
                            
                            // process managed bean
                            $eventdata = new Event\ProcessBeanImpl($decorator, $decorator->getAnnotatedType());
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            $manager->addDecorator($decorator);
                        } else {
                            $bean = new \PHPCDI\Bean\ManagedBean($class, $type, $manager);
                            
                            // process injection target event
                            $eventdata = new Event\ProcessInjectionTargetImpl($bean);
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            // process managed bean event
                            $eventdata = new Event\ProcessManagedBeanImpl($bean);
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            
                            $manager->addBean($bean);
                            $this->createProducer($bean, $type, $manager);
                            $this->createObserver($bean, $type, $manager);
                        }
                    }
            }
        }
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
            
            $bean = new \PHPCDI\Bean\ProducerMethod($declaringBean, $method, $disposer, $manager);
            
            // process producer event
            $eventdata = new Event\ProcessProducerImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
            }
            
            // process producer method
            $eventdata = new Event\ProcessProducerMethodImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
            }
                            
            $manager->addBean($bean);
        }
        
        
        foreach($class->getFieldsWithAnnotation('PHPCDI\API\Inject\Produces') as $field) {
            $bean = new \PHPCDI\Bean\ProducerField($declaringBean, $field, $manager);
            
            // process producer event
            $eventdata = new Event\ProcessProducerImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
            }
            
            // process producer method
            $eventdata = new Event\ProcessProducerFieldImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(\PHPCDI\API\Inject\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw \PHPCDI\API\DefinitionException::fromExceptionList($eventdata->getErrors());
            }
                            
            $manager->addBean($bean);
        }
    }

    private function createObserver($declaringBean, \PHPCDI\Introspector\AnnotatedTypeImpl $class, BeanManager $manager) {
        $observers = $class->getMethodsWithAnnotationOnFirstParameter('PHPCDI\API\Inject\Observes');

        foreach($observers as $observer) {
            $manager->addObserver(new \PHPCDI\Event\ObserverMethodImpl($declaringBean, $observer, $manager));
        }
    }

    private function addBuiltinBeansToManager(BeanManager $beanManager) {
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\BeanManagerBean($beanManager));
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\EventBean($beanManager));
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\InstanceBean($beanManager));
        $beanManager->addBean(new \PHPCDI\Bean\Builtin\InjectionPointBean($beanManager));
    }
}
