<?php

namespace PHPCDI\Bean;

use PHPCDI\Manager\BeanManager;
use PHPCDI\Util\Beans as BeanUtil;
use PHPCDI\Util\Annotations as AnnotationUtil;
use PHPCDI\Util\ReflectionUtil;
use PHPCDI\Injection\ManagedBeanInjectionTarget;
use PHPCDI\API\Annotations;
use PHPCDI\API\DefinitionException;
use PHPCDI\Proxy\ProxyFactory;
use PHPCDI\Proxy\ProxyObject;
use PHPCDI\Decorator\DecorationHelper;
use PHPCDI\Decorator\InterceptorAndDecoratorProxyMethodHandler;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\SPI\InjectionPoint;
use PHPCDI\SPI\InjectionTarget;
use PHPCDI\SPI\Bean;

class ManagedBean implements Bean {

    /**
     * @var \PHPCDI\SPI\InjectionTarget
     */
    private $injectionTarget;
    
    private $className;
    
    private $name;
    
    /**
     * @var \PHPCDI\SPI\AnnotatedType
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


    public function __construct($className, AnnotatedType $annotatedType, BeanManager $beanManager) {
        $this->className = $className;
        $this->annotatedType = $annotatedType;
        $this->injectionPoints = array();
        $this->initializerMethodsList = array();
        $initializerMethodsList = BeanUtil::getInitializerMethods($this, $annotatedType);
        foreach($initializerMethodsList as $method) {
            $points = BeanUtil::getParameterInjectionPoints($this, $method);
            $this->injectionPoints = \array_merge($this->injectionPoints, $points);
            $this->initializerMethodsList[] = array('method' => $method,
                                                    'injectionpoints' => $points);
        }

        $this->injectionPoints = \array_merge($this->injectionPoints, BeanUtil::getParameterInjectionPointsOfConstructor($this, $annotatedType->getConstructor()));
        $this->fieldInjectionPoints = BeanUtil::getFieldInjectionPoints($this, $annotatedType);
        $this->injectionPoints = \array_merge($this->injectionPoints, $this->fieldInjectionPoints);
        $this->qualifiers = AnnotationUtil::getQualifiers($annotatedType);
        
        if(count($this->qualifiers) == 0 || (count($this->qualifiers) == 1 
                && AnnotationUtil::listHasAnnotation($this->qualifiers, Annotations\Named::className()))) {
            $this->qualifiers[] = new Annotations\DefaultObj(array());
        }
        
        if(!AnnotationUtil::listHasAnnotation($this->qualifiers, Annotations\Any::className())) {
            $this->qualifiers[] = new Annotations\Any(array());
        }
        
        $this->stereotypes = AnnotationUtil::getStereotypes($annotatedType);
        $this->postConstructMethods = BeanUtil::getPostConstructMethods($annotatedType);
        $this->beanManager = $beanManager;
        $this->injectionTarget = new ManagedBeanInjectionTarget($this);
        
        
        // scope
        $this->scope = AnnotationUtil::getScope($annotatedType, false);
        
        if($this->scope == null) {
            $scopes = array();
            foreach($this->stereotypes as $anno) {
                if(AnnotationUtil::isScope(new \ReflectionClass($anno))) {
                    $scopes[] = get_class($anno);
                }
            }
            
            if(count($scopes) == 1) {
                $this->scope = $scopes[0];
            } else if(count($scopes) > 1) {
                throw new DefinitionException('More than one scope available via stereotypes in managed bean ' . $className);
            } else {
                $this->scope = Annotations\Dependent::className();
            }
        }
        
        // name
        $this->name = null;
        $useDefaultName = false;
        if($this->annotatedType->isAnnotationPresent(Annotations\Named::className())) {
            $namedAnnotation = $this->annotatedType->getAnnotation(Annotations\Named::className());
            
            if(empty($namedAnnotation->value)) {
                $useDefaultName = true;
            } else {
                $this->name = $namedAnnotation->value;
            }
        } else if(isset($this->stereotypes[Annotations\Named::className()]) && 
                !empty($this->stereotypes[Annotations\Named::className()]->value)) {
            throw new DefinitionException('Stereotype contains @Named annotation with a value! Stereotype used by managed bean: ' . $className);
        }
            
        if($useDefaultName || isset($this->stereotypes[Annotations\Named::className()])) {
            $startClass = strrpos($this->className, '\\');

            if($startClass !== false) {
                $this->name = ReflectionUtil::decapitalize(substr($this->className, $startClass + 1));
            } else {
                $this->name = ReflectionUtil::decapitalize($this->className);
            }
        }
    }

    public function create(CreationalContext $creationalContext) {
        $instance = $this->injectionTarget->produce($creationalContext);
        $this->injectionTarget->inject($instance, $creationalContext);
        $this->injectionTarget->postConstruct($instance);
        return $instance;
    }

    public function destroy($instance, CreationalContext $creationalContext) {
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
        return $this->name;
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

    public function createInstance(CreationalContext $creationalContext) {
        $constructorParamInjectionPoints = BeanUtil::getParameterInjectionPointsOfConstructor($this, $this->annotatedType->getConstructor());

        $args = array();
        foreach($constructorParamInjectionPoints as $injection) {
            $args[] = $this->beanManager->getInjectableReference($injection, $creationalContext);
        }
        
        $reflectionClass = $this->annotatedType->getPHPClass();
        
        if($this->isProxyRequired()) {
            $proxyfactory = new ProxyFactory();
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
    
    public function applyDecorators(ProxyObject $obj, CreationalContext $ctx, InjectionPoint $ij) {
        $decorationHelper = new DecorationHelper($obj, $this, $this->beanManager);
        DecorationHelper::getHelperStack()->push($decorationHelper);
        $firstDelegate = $decorationHelper->getNextDelegate($ij, $ctx);
        DecorationHelper::getHelperStack()->pop();
        
        $obj->setHandler(new InterceptorAndDecoratorProxyMethodHandler($firstDelegate));
        
        return $obj;
    }
    
    public function getAnnotatedType() {
        return $this->annotatedType;
    }
    
    public function getInjectionTarget() {
        return $this->injectionTarget;
    }
    
    public function setInjectionTarget(InjectionTarget $injectionTarget) {
        $this->injectionTarget = $injectionTarget;
    }
    
    public function __toString() {
        return "ManagedBean of class " . $this->className;
    }
}

