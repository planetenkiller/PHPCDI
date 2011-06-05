<?php

namespace PHPCDI\API;

use PHPCDI\Manager\BeanManager;
use PHPCDI\Bean\DecoratorImpl;
use PHPCDI\Bean\ManagedBean;
use PHPCDI\Bean\ProducerMethod;
use PHPCDI\Bean\ProducerField;
use PHPCDI\Bean\Builtin\ExtensionBean;
use PHPCDI\Bean\Builtin\BeanManagerBean;
use PHPCDI\Bean\Builtin\EventBean;
use PHPCDI\Bean\Builtin\InstanceBean;
use PHPCDI\Bean\Builtin\InjectionPointBean;
use PHPCDI\Event\ObserverMethodImpl;
use PHPCDI\Context\ApplicationContextImpl;
use PHPCDI\Context\DependentContextImpl;
use PHPCDI\SPI\Bootstrap\Deployment;
use PHPCDI\SPI\Bootstrap\ClassBundle;
use PHPCDI\SPI\Context\Context;
use PHPCDI\SPI\Decorator;
use PHPCDI\Introspector\AnnotatedTypeImpl;
use PHPCDI\API\Annotations as Annotations;
use PHPCDI\API\DefinitionException;
use PHPCDI\API\Container;
use PHPCDI\API\Event\ContainerInitialized;
use PHPCDI\Bootstrap\Event\BeforeBeanDiscoveryImpl;
use PHPCDI\Bootstrap\Event\AfterBeanDiscoveryImpl;
use PHPCDI\Bootstrap\Event\ProcessBeanImpl;
use PHPCDI\Bootstrap\Event\ProcessAnnotatedTypeImpl;
use PHPCDI\Bootstrap\Event\ProcessInjectionTargetImpl;
use PHPCDI\Bootstrap\Event\ProcessManagedBeanImpl;
use PHPCDI\Bootstrap\Event\ProcessProducerImpl;
use PHPCDI\Bootstrap\Event\ProcessProducerMethodImpl;
use PHPCDI\Bootstrap\Event\ProcessProducerFieldImpl;
use PHPCDI\Util\ReflectionUtil;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\Beans as BeanUtil;


/**
 * PHPCDI configruation builder to configure an new container instance.
 */
class Configuration {
    /**
     * @var \PHPCDI\SPI\Bootstrap\Deployment
     */
    private $deployment;
    private $contexts;

    public function __construct(Deployment $deployment) {
        $this->deployment = $deployment;
        $this->contexts = array();
        $this->addDefaultObjects();
    }

    private function addDefaultObjects() {
        $this->addContext(new ApplicationContextImpl())
             ->addContext(new DependentContextImpl());
    }

    /**
     * @param \PHPCDI\SPI\Context\Context $context new context
     * 
     * @return Configuration returns this
     */
    public function addContext(Context $context) {
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
            
            $bean = new ExtensionBean($beanManager, new $extensionClassName());
            $type = new AnnotatedTypeImpl($extensionClassName);
            $beanManager->addBean($bean);
            $this->createObserver($bean, $type, $beanManager);
        }
        
        // BeforeBeanDiscovery event (used by extensions)
        $eventdata = new BeforeBeanDiscoveryImpl();
        $rootManager->fireEvent($eventdata, array(Annotations\Any::newInstance()));
        
        // create beans
        $this->buildBeans($classBundleManager);
        
        // AfterBeanDiscovery event
        $eventdata = new AfterBeanDiscoveryImpl();
        $rootManager->fireEvent($eventdata, array(Annotations\Any::newInstance()));
        
        if($eventdata->getErrors()) {
            throw DefinitionException::fromExceptionList($eventdata->getErrors());
        }
        
        foreach($eventdata->getBeans() as $bean) {
            $beanClassName = $bean->getBeanClass();
            $classBundle = $this->deployment->getBundleOfClass($beanClassName);
            
            if($classBundle == null) {
                throw new DefinitionException('Bean of class ' . $beanClassName . ' has no class bundle (bean added via AfterBeanDiscovery event)');
            }
            
            // process bean event
            $eventdataSub = new ProcessBeanImpl($bean, null);//TODO get annotated type somehow
            $classBundleManager[$classBundle]->fireEvent(array($eventdataSub, $bean->getBeanClass()), array(Annotations\Any::newInstance()));

            if($eventdataSub->getErrors()) {
                throw DefinitionException::fromExceptionList($eventdataSub->getErrors());
            }
            
            if($bean instanceof Decorator) {
                $classBundleManager[$classBundle]->addDecorator($bean);
            } else {
                $classBundleManager[$classBundle]->addBean($bean);
            }
        }
        
        foreach ($eventdata->getObservers() as $observer) {
            $beanClassName = $bean->getBeanClass();
            $classBundle = $this->deployment->getBundleOfClass($beanClassName);
            
            if($classBundle == null) {
                throw new DefinitionException('Observer ' . $observer . ' has no class bundle (observer added via AfterBeanDiscovery event)');
            }
            
            $classBundleManager[$classBundle]->addObserver($observer);
        }
        
        foreach ($eventdata->getContexts() as $context) {
            $rootManager->addContext($context);
        }

        $container = new Container($this->deployment, $rootManager, $classBundleManager);
        
        $rootManager->fireEvent(new ContainerInitialized(), array());
        
        return $container;
    }

    private function buildManager(\SplObjectStorage $classBundleManager) {
        $rootmanager = new BeanManager('root');
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
               
                    $type = new AnnotatedTypeImpl($class);
                    
                    // ProcessAnnotatedType event (used by extensions)
                    $eventdata = new ProcessAnnotatedTypeImpl();
                    $eventdata->setAnnotatedType($type);
                    $manager->fireEvent(array($eventdata, $type->getBaseType()), array(Annotations\Any::newInstance()));
                    $type = $eventdata->getAnnotatedType();
                    
                    if(!$eventdata->hasVeto() && ReflectionUtil::isManagedBean($reflectionClass)) {
                        if($type->isAnnotationPresent(Annotations\Decorator::className())) {
                            $decorator = new DecoratorImpl($class, $type, $manager);
                            
                            // process managed bean
                            $eventdata = new ProcessBeanImpl($decorator, $decorator->getAnnotatedType());
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(Annotations\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            $manager->addDecorator($decorator);
                        } else {
                            $bean = new ManagedBean($class, $type, $manager);
                            
                            // process injection target event
                            $eventdata = new ProcessInjectionTargetImpl($bean);
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(Annotations\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            // process managed bean event
                            $eventdata = new ProcessManagedBeanImpl($bean);
                            $manager->fireEvent(array($eventdata, $type->getBaseType()), array(Annotations\Any::newInstance()));
                            
                            if($eventdata->getErrors()) {
                                throw DefinitionException::fromExceptionList($eventdata->getErrors());
                            }
                            
                            
                            $manager->addBean($bean);
                            $this->createProducer($bean, $type, $manager);
                            $this->createObserver($bean, $type, $manager);
                        }
                    }
            }
        }
    }

    private function createProducer($declaringBean, AnnotatedTypeImpl $class, BeanManager $manager) {
        $disposers = $class->getMethodsWithAnnotationOnFirstParameter(Annotations\Disposes::className());
        
        foreach($class->getMethodsWithAnnotation(Annotations\Produces::className()) as $method) {
            $disposer = null;
            
            foreach($disposers as $disposerMethod) {
                $params = $disposerMethod->getParameters();
                if(\in_array(AnnotationUtil::getReturnType($method->getPHPMember()), $params[0]->getTypeClosure())
                   && BeanUtil::compareQualifiers(AnnotationUtil::getQualifiers($params[0]), 
                                                  AnnotationUtil::getQualifiers($method))) {
                    if($disposer == null) {
                        $disposer = $disposerMethod;
                    } else {
                        throw new DefinitionException('more than one disposer method for producer method '.$method->getPHPMember()->class.'::'.$method->getPHPMember()->name);
                    }
                }
            }
            
            $bean = new ProducerMethod($declaringBean, $method, $disposer, $manager);
            
            // process producer event
            $eventdata = new ProcessProducerImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(Annotations\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw DefinitionException::fromExceptionList($eventdata->getErrors());
            }
            
            // process producer method
            $eventdata = new ProcessProducerMethodImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(Annotations\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw DefinitionException::fromExceptionList($eventdata->getErrors());
            }
                            
            $manager->addBean($bean);
        }
        
        
        foreach($class->getFieldsWithAnnotation(Annotations\Produces::className()) as $field) {
            $bean = new ProducerField($declaringBean, $field, $manager);
            
            // process producer event
            $eventdata = new ProcessProducerImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(Annotations\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw DefinitionException::fromExceptionList($eventdata->getErrors());
            }
            
            // process producer method
            $eventdata = new ProcessProducerFieldImpl($bean);
            $manager->fireEvent(array($eventdata, $bean->getMember()->getBaseType()), array(Annotations\Any::newInstance()));
                            
            if($eventdata->getErrors()) {
                throw DefinitionException::fromExceptionList($eventdata->getErrors());
            }
                            
            $manager->addBean($bean);
        }
    }

    private function createObserver($declaringBean, AnnotatedTypeImpl $class, BeanManager $manager) {
        $observers = $class->getMethodsWithAnnotationOnFirstParameter(Annotations\Observes::className());

        foreach($observers as $observer) {
            $manager->addObserver(new ObserverMethodImpl($declaringBean, $observer, $manager));
        }
    }

    private function addBuiltinBeansToManager(BeanManager $beanManager) {
        $beanManager->addBean(new BeanManagerBean($beanManager));
        $beanManager->addBean(new EventBean($beanManager));
        $beanManager->addBean(new InstanceBean($beanManager));
        $beanManager->addBean(new InjectionPointBean($beanManager));
    }
}
