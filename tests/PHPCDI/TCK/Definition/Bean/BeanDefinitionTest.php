<?php

namespace PHPCDI\TCK\Definition\Bean;

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
        $this->assertEquals('PHPCDI\API\Inject\Dependent', $list[0]->getScope());
    }
    
// todo port to php after full primitive types support
//   public void testIsNullable() throws Exception
//   {
//      assert getBeans(int.class).size() == 1;
//      Bean<Integer> bean = getBeans(int.class).iterator().next();
//      assert !bean.isNullable();
//      assert getBeans(Animal.class, TAME_LITERAL).size() == 1;
//      Bean<Animal> animalBean = getBeans(Animal.class, TAME_LITERAL).iterator().next();
//      assert animalBean.isNullable();
//   }
    
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
        $this->assertEquals("PHPCDI\API\Inject\Dependent", $bean->getScope());
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
