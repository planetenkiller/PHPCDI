<?php

namespace PHPCDI\Bean;

/**
 * BeanManager interface implementation
 */
class BeanManager implements \PHPCDI\API\Inject\SPI\BeanManager {

    private $id;
    private $beans;
    private $beansAccessibleFromOutside; // == $beans but without new and builtin beans
    private $observers;
    private $decorators;
    private $contexts;
    
    private $beansIterator;
    private $beansAccessibleFromOutsideIterator;
    private $observersIterator;
    private $decoratorsIterator;
    private $beanResolver;
    private $observerReslover;
    private $decoratorResolver;
    private $accessibleManagers;
    
    /**
     * @var \SplStack 
     */
    private $injectionPointStack;

    public function __construct($id, &$contexts=array(), \SplStack $injectionPointStack=null) {
        $this->id = $id;
        $this->accessibleManagers = array();
        $this->beans = new \ArrayObject(array());
        $this->beansIterator = new \ArrayIterator($this->beans);
        $this->beansAccessibleFromOutside = new \ArrayObject(array());
        $this->beansAccessibleFromOutsideIterator = new \ArrayIterator($this->beansAccessibleFromOutside);
        $this->contexts =& $contexts;
        $this->observers = new \ArrayObject(array());
        $this->observersIterator = new \ArrayIterator($this->observers);
        $this->decorators = new \ArrayObject(array());
        $this->decoratorsIterator = new \ArrayIterator($this->decorators);
        $this->injectionPointStack = ($injectionPointStack != null)? $injectionPointStack : new \SplStack();

        $beans = $this->beans;
        $manager = $this;
        $it = function () use (&$manager) {
            $mainmanager = $manager;
            return BeanManager::buildIterator($mainmanager, function(BeanManager $manager) use (&$mainmanager) {
                if($mainmanager === $manager) {
                    return $manager->getBeansIterator();
                } else {
                    return $manager->getBeansAccessibleFromOutsideIterator();
                }
            });
        };
        $this->beanResolver = new \PHPCDI\Resolution\TypeSafeBeanReslover(new \PHPCDI\Util\LazyIterator($it));
        
        $it2 = function () use (&$manager) {
            return BeanManager::buildIterator($manager, function(BeanManager $manager) {
                return $manager->getObserversIterator();
            });
        };
        $this->observerReslover = new \PHPCDI\Resolution\TypeSafeObserverReslover(new \PHPCDI\Util\LazyIterator($it2));
        
        $it3 = function () use (&$manager) {
            return BeanManager::buildIterator($manager, function(BeanManager $manager) {
                return $manager->getDecoratorsIterator();
            });
        };
        $this->decoratorResolver = new \PHPCDI\Resolution\TypeSafeDecoratorReslover(new \PHPCDI\Util\LazyIterator($it3));
    }

    public function addAccessibleBeanManager(BeanManager $manager) {
        if(!in_array($manager, $this->accessibleManagers, true)) {
            $this->accessibleManagers[] = $manager;
        }
    }

    public function addBean(\PHPCDI\API\Inject\SPI\Bean $bean) {
        $this->beans[] = $bean;
        
        if(!$bean instanceof Builtin\BuiltinBean) {
            $this->beansAccessibleFromOutside[] = $bean;
        }
    }
    
    public function addObserver(\PHPCDI\API\Inject\SPI\ObserverMethod $observer) {
        $this->observers[] = $observer;
    }
    
    public function addDecorator(\PHPCDI\API\Inject\SPI\Decorator $decorator) {
        $this->decorators[] = $decorator;
    }

    public function addContext(\PHPCDI\API\Context\SPI\Context $context) {
        if(isset($this->contexts[$context->getScope()])) {
            $this->contexts[$context->getScope()][] = $context;
        } else {
            $this->contexts[$context->getScope()] = array($context);
        }
    }

    public function createAnnotatedType($class) {

    }

    public function createCreationalContext($contextual) {
        return new \PHPCDI\Context\CreationalContextImpl($contextual);
    }

    public function createInjectionTarget($annotatedType) {

    }

    public function getBeans($beanType, $qualifiers) {
        return $this->beanResolver->reslove($beanType, $qualifiers);
    }

    /**
     * @param string $scopeType
     *
     * @return \PHPCDI\API\Context\SPI\Context
     */
    public function getContext($scopeType) {
        $activeContext = null;
        if(isset($this->contexts[$scopeType])) {
            foreach($this->contexts[$scopeType] as $context) {
                if($context->isActive()) {
                    if($activeContext == null) {
                        $activeContext = $context;
                    } else {
                        throw new \LogicException('More than one context active');
                    }
                }
            }
        }

        if($activeContext == null) {
            throw new \InvalidArgumentException('no active context found');
        }

        return $activeContext;
    }

    public function &getContexts() {
        return $this->contexts;
    }

    public function getInjectableReference($ij, $ctx) {
        if(!$ij->isDelegate()) {
            $registerInjectionPoint = $ij->getType() != 'PHPCDI\API\Inject\SPI\InjectionPoint';
            $bean = $this->resolve($this->getBeans($ij->getType(), $ij->getQualifiers()));
            
            if($bean == null) {
                throw new \LogicException('Can not satisfy injection point [' . $ij . ']');
            }
            
            if($registerInjectionPoint) {
                $this->injectionPointStack->push($ij);
            }
            
            $obj = $this->getRefernce($bean, $ij->getType(), $ctx);
            
            if($registerInjectionPoint) {
                $this->injectionPointStack->pop();
            }
            
            return $obj;
        } else {
            return \PHPCDI\Decorator\DecorationHelper::getHelperStack()->top()->getNextDelegate($ij, $ctx);
        }
    }

    public function getRefernce(\PHPCDI\API\Inject\SPI\Bean $bean, $beanType, \PHPCDI\API\Context\SPI\CreationalContext $ctx) {
        if($ctx instanceof \PHPCDI\Context\CreationalContextImpl) {
            $ctx = $ctx->getCreationalContext($bean);
        }
        return $this->getContext($bean->getScope())->get($bean, $ctx);
    }

    public function getStereotypeDefinition($stereotypeAnnotation) {

    }

    public function isNormalScope($annotationType) {

    }

    public function isQualifier($annotationType) {

    }

    public function isScope($annotationType) {

    }

    public function isStereotype($annotationType) {

    }

    public function resolve($beans) {
        if(count($beans) == 1) {
            return $beans[0];
        } else if(count($beans) > 1) {
            throw new \PHPCDI\API\AmbiguousResolutionException(
                    "ambiguous beans for dependency: " . \PHPCDI\Util\Beans::toString($beans));
        } else {
            return null;
        }
    }

    public function validate($injectionPoint) {

    }
    
    public function getCurrentInjectionPoint() {
        return $this->injectionPointStack->top();
    }
    
    public static function buildIterator($manager, $valueCallback) {
        $it = new \AppendIterator();
        $stack = new \SplObjectStorage();
        foreach(self::getIteratorsRecursive($manager, $stack, $valueCallback) as $sit) {
            $it->append($sit);
        }
        return $it;
    }

    public static function getIteratorsRecursive(BeanManager $manager, \SplObjectStorage $stack, $valueCallback) {
        $stack->attach($manager);
        $beans = array();
        $value = $valueCallback($manager);
        $beans[\spl_object_hash($value)] = $value;
        foreach($manager->accessibleManagers as $accessibleManager) {
            // break circular references
            if(!$stack->contains($accessibleManager)) {
                $beans = \array_merge($beans, self::getIteratorsRecursive($accessibleManager, $stack, $valueCallback));
            }
        }

        return $beans;
    }

    public function fireEvent($eventData, array $qualifiers) {
        $eventDataObj = is_array($eventData)? $eventData[0] : $eventData;
        
        foreach($this->resolveObserverMethods($eventData, $qualifiers) as $observerMethod) {
            $observerMethod->notify($eventDataObj);
        }
    }

    public function resolveObserverMethods($eventData, array $qualifiers) {
        $typeInfo = is_array($eventData)? array(\get_class($eventData[0]), $eventData[1]) : \get_class($eventData);
        return $this->observerReslover->reslove($typeInfo, $qualifiers);
    }
    
    public function resolveDecorators($type, array $qualifiers) {
        return $this->decoratorResolver->reslove($type, $qualifiers);
    }

    public function getBeansIterator() {
        return $this->beansIterator;
    }
    
    public function getBeansAccessibleFromOutsideIterator() {
        return $this->beansAccessibleFromOutsideIterator;
    }

    public function getObserversIterator() {
        return $this->observersIterator;
    }
    
    public function getDecoratorsIterator() {
        return $this->decoratorsIterator;
    }
    
    /**
     * @return \SplStack 
     */
    public function getInjectionPointStack() {
        return $this->injectionPointStack;
    }
}
