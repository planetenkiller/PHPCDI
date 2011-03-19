<?php

namespace PHPCDI\Bean;

/**
 * 
 */
class ManagedBean implements \PHPCDI\API\Inject\SPI\Bean {

    /**
     * @var \PHPCDI\API\Inject\SPI\InjectionTarget
     */
    private $injectionTarget;
    
    private $className;
    
    /**
     * @var \PHPCDI\API\Inject\SPI\AnnotatedType
     */
    protected $annotatedType;

    private $injectionPoints;

    private $initializerMethodsList;

    private $fieldInjectionPoints;

    private $qualifiers;

    private $stereotypes;

    private $scope;

    private $postConstructMethods;
    
    private $decorators;

    /**
     * @var BeanManager
     */
    private $beanManager;


    public function __construct($className, \PHPCDI\API\Inject\SPI\AnnotatedType $annotatedType, \PHPCDI\Bean\BeanManager $beanManager) {
        $this->className = $className;
        $this->annotatedType = $annotatedType;
        $this->injectionPoints = array();
        $this->initializerMethodsList = array();
        $initializerMethodsList = \PHPCDI\Util\Beans::getInitializerMethods($this, $annotatedType);
        foreach($initializerMethodsList as $method) {
            $points = \PHPCDI\Util\Beans::getParameterInjectionPoints($this, $method);
            $this->injectionPoints = \array_merge($this->injectionPoints, $points);
            $this->initializerMethodsList[] = array('method' => $method,
                                                    'injectionpoints' => $points);
        }

        $this->injectionPoints = \array_merge($this->injectionPoints, \PHPCDI\Util\Beans::getParameterInjectionPointsOfConstructor($this, $annotatedType->getConstructor()));
        $this->fieldInjectionPoints = \PHPCDI\Util\Beans::getFieldInjectionPoints($this, $annotatedType);
        $this->injectionPoints = \array_merge($this->injectionPoints, $this->fieldInjectionPoints);
        $this->qualifiers = \PHPCDI\Util\Annotations::getQualifiers($annotatedType);
        if(empty($this->qualifiers)) {
            $this->qualifiers[] = new \PHPCDI\API\Inject\Any(array());
            $this->qualifiers[] = new \PHPCDI\API\Inject\DefaultObj(array());
        }
        $this->stereotypes = \PHPCDI\Util\Annotations::getStereotypes($annotatedType);
        $this->postConstructMethods = \PHPCDI\Util\Beans::getPostConstructMethods($annotatedType);

        $this->scope = \PHPCDI\Util\Annotations::getScope($annotatedType);
        $this->beanManager = $beanManager;
        $this->injectionTarget = new \PHPCDI\Injection\ManagedBeanInjectionTarget($this);
    }

    public function create($creationalContext) {
        $instance = $this->injectionTarget->produce($creationalContext);
        $this->injectionTarget->inject($instance, $creationalContext);
        $this->injectionTarget->postConstruct($instance);
        return $instance;
    }

    public function destroy($instance, $creationalContext) {
        $this->injectionTarget->preDestory($instance);
        $creationalContext->release();
    }

    public function getBeanClass() {
        return $this->className;
    }

    public function getInjectionPoints() {
        return $this->injectionPoints;
    }

    public function getName() {
        return $this->className;
    }

    public function getQualifiers() {
        return $this->qualifiers;
    }

    public function getScope() {
        return $this->scope;
    }

    public function getStereotypes() {
        return $this->stereotypes;
    }

    public function getTypes() {
        return $this->annotatedType->getTypeClosure();
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }

    public function createInstance(\PHPCDI\API\Context\SPI\CreationalContext $creationalContext) {
        $constructorParamInjectionPoints = \PHPCDI\Util\Beans::getParameterInjectionPointsOfConstructor($this, $this->annotatedType->getConstructor());

        $args = array();
        foreach($constructorParamInjectionPoints as $injection) {
            $args[] = $this->beanManager->getInjectableReference($injection, $creationalContext);
        }
        
        $reflectionClass = $this->annotatedType->getPHPClass();
        
        if($this->isProxyRequired()) {
            $proxyfactory = new \PHPCDI\Proxy\ProxyFactory();
            $reflectionClass = $proxyfactory->extend($reflectionClass->name)->createClass();
        }

        if(!empty($args)) {
            return $reflectionClass->newInstanceArgs($args);
        } else {
            return $reflectionClass->newInstance();
        }
    }
    
    protected function isProxyRequired() {
        return $this->hasDecorators();
    }

    /**
     * @return BeanManager
     */
    public function getBeanManager() {
        return $this->beanManager;
    }

    public function getFieldInjectionPoints() {
        return $this->fieldInjectionPoints;
    }

    public function getInitializerMethods() {
        return $this->initializerMethodsList;
    }

    public function getPostConstructMethods() {
        return $this->postConstructMethods;
    }
    
    public function getDecorators() {
        if($this->decorators == null) {
            $this->decorators = $this->beanManager->resolveDecorators($this->getTypes(), $this->getQualifiers());
        }
        
        return $this->decorators;
    }
    
    public function hasDecorators() {
        return $this->getDecorators() != null && \count($this->getDecorators()) > 0;
    }
    
    public function applyDecorators(\PHPCDI\Proxy\ProxyObject $obj, \PHPCDI\API\Context\SPI\CreationalContext $ctx, \PHPCDI\API\Inject\SPI\InjectionPoint $ij) {
        $decorationHelper = new \PHPCDI\Decorator\DecorationHelper($obj, $this, $this->beanManager);
        \PHPCDI\Decorator\DecorationHelper::getHelperStack()->push($decorationHelper);
        $firstDelegate = $decorationHelper->getNextDelegate($ij, $ctx);
        \PHPCDI\Decorator\DecorationHelper::getHelperStack()->pop();
        
        $obj->setHandler(new \PHPCDI\Decorator\InterceptorAndDecoratorProxyMethodHandler($firstDelegate));
        
        return $obj;
    }
}

