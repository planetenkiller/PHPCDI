<?php

namespace PHPCDI\Proxy;

require_once __DIR__ . '/../../bootstrap.php';

class ProxyFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testInterface() {
        $handler = $this->getMock('PHPCDI\Proxy\MethodHandler');
        $handler->expects($this->once())
                ->method('invoke')
                ->will($this->returnValue(true));
        
        $factory = new ProxyFactory();
        $obj = $factory->implement('PHPCDI\Proxy\ProxyFactoryTest_InterfaceA')->createInstance(array(), $handler);
        
        $this->assertNotNull($obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyObject', $obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyFactoryTest_InterfaceA', $obj);
        
        $this->assertTrue($obj->log('message'));
    }
    
    public function testInterfaces() {
        $handler = $this->getMock('PHPCDI\Proxy\MethodHandler');
        $handler->expects($this->exactly(2))
                ->method('invoke')
                ->will($this->returnValue(true));
        
        $factory = new ProxyFactory();
        $obj = $factory->implement('PHPCDI\Proxy\ProxyFactoryTest_InterfaceA')
                ->implement('PHPCDI\Proxy\ProxyFactoryTest_InterfaceB')
                ->createInstance(array(), $handler);
        
        $this->assertNotNull($obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyObject', $obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyFactoryTest_InterfaceA', $obj);
        
        $this->assertTrue($obj->log('message'));
        $this->assertTrue($obj->logB('message'));
    }
    
    public function testSuperclass() {
        $handler = $this->getMock('PHPCDI\Proxy\MethodHandler');
        $handler->expects($this->once())
                ->method('invoke')
                ->will($this->returnValue(true));
        
        $factory = new ProxyFactory();
        $obj = $factory->extend('PHPCDI\Proxy\ProxyFactoryTest_SuperClass')->createInstance(array(), $handler);
        
        $this->assertNotNull($obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyObject', $obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyFactoryTest_SuperClass', $obj);
        
        $this->assertTrue($obj->logAbstract('message'));
    }
    
    public function testSuperclassWithFinalMethod() {
        $handler = $this->getMock('PHPCDI\Proxy\MethodHandler');
        $handler->expects($this->once())
                ->method('invoke')
                ->will($this->returnValue(true));
        
        $factory = new ProxyFactory();
        $obj = $factory->extend('PHPCDI\Proxy\ProxyFactoryTest_SuperClassWithFinalMethod')->createInstance(array(), $handler);
        
        $this->assertNotNull($obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyObject', $obj);
        $this->assertInstanceOf('PHPCDI\Proxy\ProxyFactoryTest_SuperClassWithFinalMethod', $obj);
        
        $this->assertTrue($obj->logAbstract('message'));
        $this->assertFalse($obj->logFinal('message'));
    }
}

interface ProxyFactoryTest_InterfaceA {
    public function log($msg);
}

interface ProxyFactoryTest_InterfaceB {
    public function logB($msg);
}

abstract class ProxyFactoryTest_SuperClass {
    public abstract function logAbstract($msg);
}

abstract class ProxyFactoryTest_SuperClassWithFinalMethod {
    public abstract function logAbstract($msg);
    public final function logFinal($msg) {
        return false;
    }
}
