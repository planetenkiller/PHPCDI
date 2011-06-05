<?php

namespace PHPCDI\TCK\Definition\Bean;

use PHPCDI\API\Annotations;

require_once __DIR__ . '/../../../../bootstrap.php';

class BeanDefinitionTest extends \PHPCDI\TCK\AbstractTckTest {
    public function testBeanTypesNonEmpty() {
        $this->assertEquals(1, count($this->getBeans('RedSnapper')));
        
        $list = $this->getBeans('RedSnapper');
        $this->assertGreaterThan(0, count($list[0]->getTypes()));
    }
    
    public function testQualifiersNonEmpty() {
        $this->assertEquals(1, count($this->getBeans('RedSnapper')));
        
        $list = $this->getBeans('RedSnapper');
        $this->assertGreaterThan(0, count($list[0]->getQualifiers()));
    }
    
    public function testHasScopeType() {
        $this->assertEquals(1, count($this->getBeans('RedSnapper')));
        
        $list = $this->getBeans('RedSnapper');
        $this->assertEquals(Annotations\Dependent::className(), $list[0]->getScope());
    }
    
    public function testIsNullable() {
        $this->assertEquals(1, count($this->getBeans('int', array(), false)));
        $this->assertEquals(1, count($this->getBeans('integer', array(), false)));
        $bean = $this->getBean('int', array(), false);
        $this->assertFalse($bean->isNullable());
        
        $this->assertEquals(1, count($this->getBeans('Animal', array(Tame::newInstance()))));
        $bean = $this->getBeans('Animal', array(Tame::newInstance()));
        $bean = $bean[0];
        $this->assertTrue($bean->isNullable());
    }
    
    public function testBeanTypes() {
        $bean = $this->getBean('Tarantula');
        
        $this->assertEquals(6, count($bean->getTypes()));
        $this->assertContains('PHPCDI\TCK\Definition\Bean\Tarantula', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\Spider', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\Animal', $bean->getTypes());
        $this->assertContains('mixed', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\DeadlySpider', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\DeadlyAnimal', $bean->getTypes());
    }
    
    public function testBeanClientCanCastBeanInstanceToAnyBeanType() {
        $tarantula = $this->getInstanceViaContext('Tarantula');
        
        $this->assertInstanceOf('PHPCDI\TCK\Definition\Bean\Tarantula', $tarantula);
        $this->assertInstanceOf('PHPCDI\TCK\Definition\Bean\Spider', $tarantula);
        $this->assertInstanceOf('PHPCDI\TCK\Definition\Bean\Animal', $tarantula);
        $this->assertInstanceOf('PHPCDI\TCK\Definition\Bean\DeadlySpider', $tarantula);
        $this->assertInstanceOf('PHPCDI\TCK\Definition\Bean\DeadlyAnimal', $tarantula);
    }
    
    public function testAbstractApiType() {
        $bean = $this->getBean('FriendlyAntelope');
        
        $this->assertEquals(4, count($bean->getTypes()));
        $this->assertContains('PHPCDI\TCK\Definition\Bean\FriendlyAntelope', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\AbstractAntelope', $bean->getTypes());
        $this->assertContains('PHPCDI\TCK\Definition\Bean\Animal', $bean->getTypes());
        $this->assertContains('mixed', $bean->getTypes());
    }
    
    public function testFinalApiType() {
        $this->assertGreaterThan(0, $this->getBeans('DependentFinalTuna'));
    }
    
    public function testMultipleStereotypes() {
        $bean = $this->getBean('ComplicatedTuna');
        $this->assertEquals(Annotations\Dependent::className(), $bean->getScope());
        $this->assertEquals("complicatedTuna", $bean->getName());
    }
    
    public function testBeanExtendsAnotherBean() {
        $this->assertGreaterThan(0, $this->getBeans('Spider'));
        $this->assertGreaterThan(0, $this->getBeans('Tarantula'));
    }
    
    public function testBeanClassOnSimpleBean() {
        $this->assertEquals(1, count($this->getBeans('Horse')));
        
        $this->assertEquals("PHPCDI\TCK\Definition\Bean\Horse", $this->getBean('Horse')->getBeanClass());
    }
}

