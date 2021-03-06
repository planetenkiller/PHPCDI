<?php

namespace PHPCDI\Decorator;

use PHPCDI\SPI\InjectionPoint;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\Bean\ManagedBean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\Proxy\ProxyObject;

class DecorationHelper {
    private static $helperStack;
    private $decorators;
    private $position;
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    private $previousDelegate;
    private $originalInstance;
    
    public function __construct($originalInstance, ManagedBean $managedBean, BeanManager $beanManager) {
        $this->originalInstance = $originalInstance;
        $this->decorators = $managedBean->getDecorators();
        $this->position = 0;
        $this->beanManager = $beanManager;
    }
    
    /**
     * @return \SplStack 
     */
    public static function getHelperStack() {
        if(self::$helperStack == null) {
            self::$helperStack = new \SplStack();
        }
        
        return self::$helperStack;
    }
    
    public function getNextDelegate(InjectionPoint $ij, CreationalContext $ctx) {
        if($this->position == \count($this->decorators)) {
            $this->previousDelegate = $this->originalInstance;
            return $this->originalInstance;
        }
        
        /* @var $currentDecorator \PHPCDI\SPI\Decorator */
        $currentDecorator = $this->decorators[$this->position++];
        $currentDecoratorObj = $this->beanManager->getRefernce($currentDecorator, $currentDecorator->getBeanClass(), $ctx);
        if($currentDecoratorObj instanceof ProxyObject) {
            $currentDecoratorObj->setHandler(new DecoratorProxyHandler($this->previousDelegate));
        }
        
        $this->previousDelegate = $currentDecoratorObj;
        return $currentDecoratorObj;
    }
}

