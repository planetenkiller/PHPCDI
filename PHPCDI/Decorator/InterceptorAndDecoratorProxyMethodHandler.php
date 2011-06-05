<?php

namespace PHPCDI\Decorator;

use PHPCDI\Proxy\MethodHandler;

class InterceptorAndDecoratorProxyMethodHandler implements MethodHandler {
    private static $disabledHandlers;
    
    /**
     * @return \SplObjectStorage
     */
    public static function getDisabledHandlers() {
        if(self::$disabledHandlers == null) {
            self::$disabledHandlers = new \SplObjectStorage();
        }
        
        return self::$disabledHandlers;
    }
    
    
    private $firstDecorator;
    
    public function __construct($firstDecorator) {
        $this->firstDecorator = $firstDecorator;
    }

    public function invoke($proxy, $declaredMethod, $overriddenMethod, array $args) {
        if(!self::getDisabledHandlers()->contains($this)) {
            try {
                self::getDisabledHandlers()->attach($this);
                
                return \call_user_method_array($declaredMethod['method'], $this->firstDecorator, $args);
            } catch (Exception $e) {
                self::getDisabledHandlers()->detach($this);
                throw $e;
            }
            
            self::getDisabledHandlers()->detach($this);
        } else {
            $method = new \ReflectionMethod($overriddenMethod['class'], $overriddenMethod['method']);
            return $method->invokeArgs($proxy, $args);
        }
    }
}

