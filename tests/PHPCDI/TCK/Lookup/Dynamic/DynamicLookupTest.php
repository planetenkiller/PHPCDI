<?php

namespace PHPCDI\TCK\Lookup\Dynamic;

use PHPCDI\API\Inject\DefaultObj;

require_once __DIR__ . '/../../../../bootstrap.php';

class DynamicLookupTest extends \PHPCDI\TCK\AbstractTckTest {
    public function testObtainsInjectsInstanceOfInstance() {
        $obj = $this->getInstanceViaContext('ObtainsInstanceBean');
        
        $this->assertNotNull($obj->getPaymentProcessor());
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testDuplicateBindingsThrowsException() {
        $obj = $this->getInstanceViaContext('ObtainsInstanceBean');
        
        $obj->getAnyPaymentProcessor()->select(array(DefaultObj::newInstance(), DefaultObj::newInstance()));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonBindingThrowsException() {
        $obj = $this->getInstanceViaContext('ObtainsInstanceBean');
        
        $obj->getAnyPaymentProcessor()->select(array(NonBinding::newInstance()));
    }
    
    public function testGetMethod() {
        // setup
        $this->getInstanceViaContext('AdvancedPaymentProcessor')->setValue(10);
        
        $instanceObject = $this->getInstanceViaContext('ObtainsInstanceBean')->getPaymentProcessor();
        
        $this->assertInstanceOf('PHPCDI\TCK\Lookup\Dynamic\AdvancedPaymentProcessor', $instanceObject->get());
        $this->assertEquals(10, $instanceObject->get()->getValue());
    }
    
    /**
     * @expectedException PHPCDI\API\UnsatisfiedResolutionException
     */
    public function testUnsatisfiedDependencyThrowsException() {
         echo $this->getInstanceViaContext('ObtainsInstanceBean')->getPaymentProcessor()->selectInstance('PHPCDI\TCK\Lookup\Dynamic\RemotePaymentProcessor', array())->get();
    }
    
    /**
     * @expectedException PHPCDI\API\AmbiguousResolutionException
     */
    public function testAmbiguousDependencyThrowsException() {
        $this->getInstanceViaContext('ObtainsInstanceBean')->getAnyPaymentProcessor()->get();
    }
    
    public function testIteratorMethod() {
        // setup
        $this->getInstanceViaContext('AdvancedPaymentProcessor')->setValue(1);
        $this->getInstanceViaContext('RemotePaymentProcessor')->setValue(2);
        
        $instance = $this->getInstanceViaContext('ObtainsInstanceBean')->getAnyPaymentProcessor();
        
        $advanced = null;
        $remote = null;
        $instances = 0;
        foreach($instance->selectInstance('PHPCDI\TCK\Lookup\Dynamic\AsynchronousPaymentProcessor', array()) as $obj) {
            if($obj instanceof AdvancedPaymentProcessor) {
                $advanced = $obj;
            } else if($obj instanceof RemotePaymentProcessor) {
                $remote = $obj;
            } else {
                $this->fail('iterator: unexpected instance');
            }
            
            $instances++;
        }
        
        $this->assertEquals(2, $instances);
        $this->assertNotNull($advanced);
        $this->assertEquals(1, $advanced->getValue());
        $this->assertNotNull($remote);
        $this->assertEquals(2, $remote->getValue());
        
        $instances = 0;
        foreach($instance->selectInstance('PHPCDI\TCK\Lookup\Dynamic\RemotePaymentProcessor', array()) as $obj) {
            if(!$obj instanceof RemotePaymentProcessor || $obj->getValue() != 2) {
                $this->fail('iterator: unexpected instance/value');
            }
            
            $instances++;
        }
        
        $this->assertEquals(1, $instances);
    }
    
    public function testIsUnsatisfied() {
        $obj = $this->getInstanceViaContext('ObtainsInstanceBean');
        
        $this->assertFalse($obj->getAnyPaymentProcessor()->isUnsatisfied());
        $this->assertTrue($obj
                ->getPaymentProcessor()
                ->selectInstance('PHPCDI\TCK\Lookup\Dynamic\RemotePaymentProcessor', array())
                ->isUnsatisfied());
    }
    
    public function testIsAmbiguous() {
        $obj = $this->getInstanceViaContext('ObtainsInstanceBean');
        
        $this->assertTrue($obj->getAnyPaymentProcessor()->isAmbiguous());
        $this->assertFalse($obj->getPaymentProcessor()->isAmbiguous());
    }
}
