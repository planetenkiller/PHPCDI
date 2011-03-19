<?php

namespace PHPCDI\Decorator;

use \PHPCDI\API\Inject\SPI\InjectionPoint;
use \PHPCDI\API\Context\SPI\CreationalContext;

class DecorationHelper {
    private static $helperStack;
    private $decorators;
    private $position;
    /**
     * @var \PHPCDI\Bean\BeanManager 
     */
    private $beanManager;
    private $previousDelegate;
    private $originalInstance;
    
    public function __construct($originalInstance, \PHPCDI\Bean\ManagedBean $managedBean, \PHPCDI\Bean\BeanManager $beanManager) {
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
        
        /* @var $currentDecorator \PHPCDI\API\Inject\SPI\Decorator */
        $currentDecorator = $this->decorators[$this->position++];
        $currentDecoratorObj = $this->beanManager->getRefernce($currentDecorator, $currentDecorator->getBeanClass(), $ctx);
        if($currentDecoratorObj instanceof \PHPCDI\Proxy\ProxyObject) {
            $currentDecoratorObj->setHandler(new DecoratorProxyHandler($this->previousDelegate));
        }
        
        $this->previousDelegate = $currentDecoratorObj;
        return $currentDecoratorObj;
    }
}

