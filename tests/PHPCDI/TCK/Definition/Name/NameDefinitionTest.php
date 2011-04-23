<?php

namespace PHPCDI\TCK\Definition\Name;

require_once __DIR__ . '/../../../../bootstrap.php';


class NameDefinitionTest extends \PHPCDI\TCK\AbstractTckTest {
    public function testNonDefaultNamed() {
        $bean = $this->getBean('Moose');
        
        $this->assertEquals("aMoose", $bean->getName());
    }
    
    public function testDefaultNamed() {
        $bean = $this->getBean('Haddock');
        
        $this->assertEquals("haddock", $bean->getName());
    }
    
    public function testStereotypeDefaultsName() {
        $bean = $this->getBean('RedSnapper');
        
        $this->assertEquals("redSnapper", $bean->getName());
    }
    
    public function testNotNamedInJava() {
        $bean = $this->getBean('SeaBass');
        
        $this->assertNull($bean->getName());
    }
    
    public function testNotNamedInStereotype() {
        $bean = $this->getBean('Minnow');
        
        $this->assertNull($bean->getName());
    }
}
