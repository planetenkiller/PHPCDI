<?php

namespace PHPCDI\Util;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Test class for Annotations.
 */
class AnnotationsTest extends \PHPUnit_Framework_TestCase {

    public function testGetQualifiersOnParamteter() {
        $param = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedParameter');
        $method = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedCallable');

        $param->expects($this->once())
            ->method('getDeclaringCallable')
            ->will($this->returnValue($method));

        $param->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('a'));

        $method->expects($this->once())
            ->method('getPHPMember')
            ->will($this->returnValue(new \ReflectionMethod('PHPCDI\Util\AnnotationsTestQualifierAnnotation', 'test')));


        $qualifiers = Annotations::getQualifiers($param);

        $this->assertNotNull($qualifiers);
        $this->assertEquals(1, count($qualifiers));
        $this->assertInstanceOf('PHPCDI\API\Inject\DefaultObj', $qualifiers[0]);
    }

    public function testGetQualifiersOnClass() {
        $field = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedType');

        $field->expects($this->once())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));

        $qualifiers = Annotations::getQualifiers($field);

        $this->assertNotNull($qualifiers);
        $this->assertEquals(1, count($qualifiers));
        $this->assertInstanceOf('PHPCDI\API\Inject\DefaultObj', $qualifiers[0]);
    }

    public function testGetQualifiersOnField() {
        $class = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedField');

        $class->expects($this->once())
            ->method('getPHPMember')
            ->will($this->returnValue(new \ReflectionProperty('PHPCDI\Util\AnnotationsTestQualifierAnnotation', 'prop')));

        $qualifiers = Annotations::getQualifiers($class);

        $this->assertNotNull($qualifiers);
        $this->assertEquals(1, count($qualifiers));
        $this->assertInstanceOf('PHPCDI\API\Inject\DefaultObj', $qualifiers[0]);
    }

    public function testIsQualifier() {
        $this->assertTrue(Annotations::isQualifier(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));
    }

    public function testIsQualifierNonQualifierAnnotation() {
        $this->assertFalse(Annotations::isQualifier(new \ReflectionClass('PHPCDI\Util\AnnotationsTest')));
    }

    public function testIsStereotype() {
        $this->assertTrue(Annotations::isStereotype(new \ReflectionClass('PHPCDI\Util\AnnotationsTestStereotypeAnnotation')));
    }

    public function testIsStereotypeNonQualifierAnnotation() {
        $this->assertFalse(Annotations::isStereotype(new \ReflectionClass('PHPCDI\Util\AnnotationsTest')));
    }

    public function testGetStereotypes() {
        $class = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedType');

        $class->expects($this->once())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));

        $stereotypes = Annotations::getStereotypes($class);

        $this->assertNotNull($stereotypes);
        $this->assertEquals(2, count($stereotypes));
        $this->assertInstanceOf('PHPCDI\API\Inject\DefaultObj', $stereotypes['PHPCDI\Util\AnnotationsTestStereotypeAnnotation'][0]);
        $this->assertInstanceOf('PHPCDI\API\Inject\Any', $stereotypes['PHPCDI\Util\AnnotationsTestStereotypeAnnotation2'][0]);
    }

    public function testIsScope() {
        $this->assertTrue(Annotations::isScope(new \ReflectionClass('PHPCDI\API\Inject\ApplicationScoped')));
        $this->assertTrue(Annotations::isScope(new \ReflectionClass('PHPCDI\API\Inject\Dependent')));
    }

    public function testIsScopeNonScopeAnnotations() {
        $this->assertFalse(Annotations::isScope(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));
    }

    public function testGetScope() {
        $class = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedType');

        $class->expects($this->atLeastOnce())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestApplicationScope')));

        $scope = Annotations::getScope($class);

        $this->assertNotNull($scope);
        $this->assertEquals('PHPCDI\API\Inject\ApplicationScoped', $scope);
    }

    public function testGetScopedDefaultScope() {
        $class = $this->getMock('PHPCDI\API\Inject\SPI\AnnotatedType');

        $class->expects($this->atLeastOnce())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTest')));

        $scope = Annotations::getScope($class);

        $this->assertNotNull($scope);
        $this->assertEquals('PHPCDI\API\Inject\Dependent', $scope);
    }
}

/**
 * @Qualifier
 * @DefaultObj
 * @PHPCDI\Util\AnnotationsTestStereotypeAnnotation
 */
class AnnotationsTestQualifierAnnotation extends \Doctrine\Common\Annotations\Annotation {
    /**
     * @Inject @DefaultObj
     * @var PHPCDI\Util\AnnotationsTestStereotypeAnnotation
     */
    private $prop;

    /**
     * @P(name="a",value={@DefaultObj})
     */
    public function test($a) {

    }
}

/**
 * @Stereotype
 * @DefaultObj
 * @PHPCDI\Util\AnnotationsTestStereotypeAnnotation2
 */
class AnnotationsTestStereotypeAnnotation extends \Doctrine\Common\Annotations\Annotation {
}

/**
 * @Stereotype
 * @Any
 */
class AnnotationsTestStereotypeAnnotation2 extends \Doctrine\Common\Annotations\Annotation {
}

/**
 * @ApplicationScoped
 */
class AnnotationsTestApplicationScope {
}
/**
 * @Dependent
 */
class AnnotationsTestDependentScope {
}