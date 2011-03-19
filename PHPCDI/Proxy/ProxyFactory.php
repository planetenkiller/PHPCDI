<?php

namespace PHPCDI\Proxy;

/**
 * Factory to create proxy classes at runtime.
 */
class ProxyFactory {
    private $superclass;
    private $interfaces;
    private $methodFilter;
    
    public function __construct() {
        $this->interfaces = array();
        $this->methodFilter = function ($method) { return true; };
    }
    
    /**
     * Sets the class the proxy should extend.
     * 
     * @return ProxyFactory
     */
    public function extend($class) {
        $this->superclass = $class;
        return $this;
    }
    
    /**
     * Add an interface the proxy should implement.
     * 
     * @return ProxyFactory
     */
    public function implement($interface) {
        $this->interfaces[] = $interface;
        return $this;
    }
    
    /**
     * @return ProxyFactory
     */
    public function setMethodFilter($filterCallback) {
        $this->methodFilter = $filterCallback;
        return $this;
    }
    
    /**
     * @return ProxyObject
     */
    public function createInstance(array $constructorArgs=array(), MethodHandler $handler=null) {
        $class = $this->createClass();
        
        if(!empty($constructorArgs)) {
            $obj = $class->newInstanceArgs($constructorArgs);
        } else {
            $obj = $class->newInstance();
        }
        
        if($handler != null) {
            $obj->setHandler($handler);
        }
        
        return $obj;
    }
    
    /**
     * @return \ReflectionClass
     */
    public function createClass() {
        $namespace = self::PROXY_NAMESPACE;
        $proxyClassName = $this->buildProxyClassName();
        $extends = '';
        $methods = array();
        
        if(!\class_exists($namespace . '\\' . $proxyClassName)) {
            if(!empty($this->superclass)) {
                $extends = 'extends \\' . $this->superclass;

                $overwrittenMethods = array();

                for($class=new \ReflectionClass($this->superclass); $class != null; $class = $class->getParentClass()) {
                    foreach($class->getMethods() as $method) {
                        /* @var $method \ReflectionMethod */

                        if(!$method->isStatic()
                               && !$method->isFinal()
                               && $method->isPublic()
                               && !\in_array($method->class."::".$method->name, $overwrittenMethods)) {
                            try {
                                $origMethod = $method->getPrototype();
                                $overwrittenMethods[] = $origMethod->class."::".$origMethod->name;
                            } catch (\ReflectionException $e) {}

                            $methodFilter = $this->methodFilter;
                            if($methodFilter($method)) {
                                list($argsDeclaration, $argsArray, $argsUse) = $this->buildMethodParameterData($method);

                                $origMethod = $method;
                                try {
                                    if($origMethod->getPrototype()) {
                                        $origMethod = $origMethod->getPrototype();
                                    }
                                } catch (\ReflectionException $e) {}
                                $declaredMethod = "array('class' => '" . $origMethod->class . "', 'method' => '". $origMethod->name ."')";
                                $overriddenMethod = "array('class' => '" . $method->class . "', 'method' => '". $method->name ."')";


                                $methods[] = \str_replace(array('<name>', '<argsDeclaration>', 
                                                                '<declaredMethod>', '<overriddenMethod>',
                                                                '<argsArray>', '<argsUse>'), 
                                                          array($method->name, $argsDeclaration, 
                                                                $declaredMethod, $overriddenMethod,
                                                                $argsArray, $argsUse), 
                                                          self::PROXY_OVERRIDDE_METHOD_TEMPLATE);
                            }
                        }
                    }
                }
            }

            $implements = '';
            foreach($this->interfaces as $interface) {
                $implements .= ', \\' . $interface;

                $class = new \ReflectionClass($interface);
                foreach($class->getMethods() as $method) {
                    list($argsDeclaration, $argsArray, $argsUse) = $this->buildMethodParameterData($method);

                    $declaredMethod = "array('class' => '" . $method->class . "', 'method' => '". $method->name ."')";
                    $overriddenMethod = 'null';

                    $methods[] = \str_replace(array('<name>', '<argsDeclaration>', 
                                                    '<declaredMethod>', '<overriddenMethod>',
                                                    '<argsArray>'), 
                                              array($method->name, $argsDeclaration, 
                                                    $declaredMethod, $overriddenMethod,
                                                    $argsArray), 
                                              self::PROXY_INTERFACE_METHOD_TEMPLATE);
                }
            }


            $methodsPHPCode = \implode("\n", $methods);
            $classPHPCode = \str_replace(array('<NameSpace>', '<ProxyClassName>',
                                               '<Extends>', '<Implements>',
                                               '<Methods>'), 
                                         array($namespace, $proxyClassName,
                                               $extends, $implements,
                                               $methodsPHPCode), 
                                         self::PROXY_CLASS_TEMPLATE);


            eval($classPHPCode); //TODO: remove this and use an file cache with autoloading
        }
        
        return new \ReflectionClass($namespace . '\\' . $proxyClassName);
    }
    
    protected function buildProxyClassName() {
        $className = '';
        
        if(!empty($this->superclass)) {
            $className .= \str_replace('\\', '', $this->superclass);
        }
        
        foreach($this->interfaces as $interface) {
            $className .= \str_replace('\\', '', $interface);
        }
        
        return $className;
    }
    
    protected function buildMethodParameterData(\ReflectionMethod $method) {
        $firstParam = true;
        $argsDeclaration = '';
        $argsArray = 'array(';
        $argsUse = '';

        foreach ($method->getParameters() as $param) {
            if ($firstParam) {
                $firstParam = false;
            } else {
                $argsDeclaration .= ', ';
                $argsArray .= ', ';
                $argsUse .= ', ';
            }

            if (($paramClass = $param->getClass()) !== null) {
                $argsDeclaration .= '\\' . $paramClass->getName() . ' ';
            } else if ($param->isArray()) {
                $argsDeclaration .= 'array ';
            }

            if ($param->isPassedByReference()) {
                $argsDeclaration .= '&';
            }

            $argsDeclaration .= '$' . $param->getName();
            $argsArray .= "'" . $param->getName() . "' => $" . $param->getName();
            $argsUse .= '$' . $param->getName();

            if ($param->isDefaultValueAvailable()) {
                $argsDeclaration .= ' = ' . var_export($param->getDefaultValue(), true);
            }
        }
        
        $argsArray .= ')';
        
        return array($argsDeclaration, $argsArray, $argsUse);
    }

    
    const PROXY_NAMESPACE = 'PHPCDI\Proxy\Generated';
    const PROXY_CLASS_TEMPLATE = <<<'CLASS'
namespace <NameSpace>;
    
class <ProxyClassName> <Extends> implements \PHPCDI\Proxy\ProxyObject<Implements> {
    private $__proxyHandler__;

    public function getHandler() {
        return $this->__proxyHandler__;
    }
    
    public function setHandler(\PHPCDI\Proxy\MethodHandler $handler) {
        $this->__proxyHandler__ = $handler;
    }
    
    <Methods>
}
CLASS;
    
    const PROXY_OVERRIDDE_METHOD_TEMPLATE = <<<'METHOD'
public function <name>(<argsDeclaration>) {
    if($this->__proxyHandler__) {
        return $this->__proxyHandler__->invoke($this, <declaredMethod>, <overriddenMethod>, <argsArray>);
    } else {
        return parent::<name>(<argsUse>);
    }
}
METHOD;
    
    const PROXY_INTERFACE_METHOD_TEMPLATE = <<<'METHOD'
public function <name>(<argsDeclaration>) {
    if($this->__proxyHandler__) {
        return $this->__proxyHandler__->invoke($this, <declaredMethod>, <overriddenMethod>, <argsArray>);
    }
}
METHOD;
}
