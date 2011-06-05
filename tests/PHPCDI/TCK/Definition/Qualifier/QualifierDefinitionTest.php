<?php

namespace PHPCDI\TCK\Definition\Qualifier;

use PHPCDI\API\Annotations;

require_once __DIR__ . '/../../../../bootstrap.php';

class QualifierDefinitionTest extends \PHPCDI\TCK\AbstractTckTest {
    public function testDefaultQualifierDeclaredInJava() {
        $bean = $this->getBean('Order');
        
        $this->assertEquals(2, count($bean->getQualifiers()));
        $a = $bean->getQualifiers();
        $this->assertInstanceOf(Annotations\DefaultObj::className(), $a[0]);
        $this->assertInstanceOf(Annotations\Any::className(), $a[1]);
    }
    
    public function testDefaultQualifierForInjectionPoint() {
        $bean = $this->getBean('Order');
        
        $this->assertEquals(1, count($bean->getInjectionPoints()));
        $ip = $bean->getInjectionPoints();
        $ip = $ip[0];
        
        $a = $ip->getQualifiers();
        $this->assertInstanceOf(Annotations\DefaultObj::className(), $a[0]);
    }
    
    public function testQualifierDeclaresBindingAnnotation() {
        $this->assertNotEmpty($this->getBeans('Tarantula', array(Tame::newInstance())));
    }
    
    public function testQualifiersDeclaredInJava() {
        $bean = $this->getBeans('Cat', array(Synchronous::newInstance()));
        
        $this->assertEquals(1, count($bean));
        $bean = $bean[0];
        
        $this->assertEquals(2, count($bean->getQualifiers()));
        
        $list = $bean->getQualifiers();
        $this->assertInstanceOf(Synchronous::className(), $list[0]);
    }
}
