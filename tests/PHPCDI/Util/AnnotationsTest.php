<?php

namespace PHPCDI\Util;

use PHPCDI\API\Annotations as AnnotationsPkg;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Test class for Annotations.
 */
class AnnotationsTest extends \PHPUnit_Framework_TestCase {

    public function testGetQualifiersOnParamteter() {
        $param = $this->getMock('PHPCDI\SPI\AnnotatedParameter');
        $method = $this->getMock('PHPCDI\SPI\AnnotatedCallable');

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
        $this->assertInstanceOf(AnnotationsPkg\DefaultObj::className(), $qualifiers[0]);
    }

    public function testGetQualifiersOnClass() {
        $field = $this->getMock('PHPCDI\SPI\AnnotatedType');

        $field->expects($this->once())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));

        $qualifiers = Annotations::getQualifiers($field);

        $this->assertNotNull($qualifiers);
        $this->assertEquals(1, count($qualifiers));
        $this->assertInstanceOf(AnnotationsPkg\DefaultObj::className(), $qualifiers[0]);
    }

    public function testGetQualifiersOnField() {
        $class = $this->getMock('PHPCDI\SPI\AnnotatedField');

        $class->expects($this->once())
            ->method('getPHPMember')
            ->will($this->returnValue(new \ReflectionProperty('PHPCDI\Util\AnnotationsTestQualifierAnnotation', 'prop')));

        $qualifiers = Annotations::getQualifiers($class);

        $this->assertNotNull($qualifiers);
        $this->assertEquals(1, count($qualifiers));
        $this->assertInstanceOf(AnnotationsPkg\DefaultObj::className(), $qualifiers[0]);
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
        $class = $this->getMock('PHPCDI\SPI\AnnotatedType');

        $class->expects($this->once())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));

        $stereotypes = Annotations::getStereotypes($class);

        $this->assertNotNull($stereotypes);
        $this->assertEquals(2, count($stereotypes));
        $this->assertInstanceOf(AnnotationsPkg\DefaultObj::className(), $stereotypes[AnnotationsPkg\DefaultObj::className()]);
        $this->assertInstanceOf(AnnotationsPkg\Any::className(), $stereotypes[AnnotationsPkg\Any::className()]);
    }

    public function testIsScope() {
        $this->assertTrue(Annotations::isScope(new \ReflectionClass(AnnotationsPkg\ApplicationScoped::className())));
        $this->assertTrue(Annotations::isScope(new \ReflectionClass(AnnotationsPkg\Dependent::className())));
    }

    public function testIsScopeNonScopeAnnotations() {
        $this->assertFalse(Annotations::isScope(new \ReflectionClass('PHPCDI\Util\AnnotationsTestQualifierAnnotation')));
    }

    public function testGetScope() {
        $class = $this->getMock('PHPCDI\SPI\AnnotatedType');

        $class->expects($this->atLeastOnce())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTestApplicationScope')));

        $scope = Annotations::getScope($class);

        $this->assertNotNull($scope);
        $this->assertEquals(AnnotationsPkg\ApplicationScoped::className(), $scope);
    }

    public function testGetScopedDefaultScope() {
        $class = $this->getMock('PHPCDI\SPI\AnnotatedType');

        $class->expects($this->atLeastOnce())
            ->method('getPHPClass')
            ->will($this->returnValue(new \ReflectionClass('PHPCDI\Util\AnnotationsTest')));

        $scope = Annotations::getScope($class);

        $this->assertNotNull($scope);
        $this->assertEquals(AnnotationsPkg\Dependent::className(), $scope);
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