<?php

namespace PHPCDI\Util;

require_once __DIR__ . '/../../bootstrap.php';


class AnnotationParserTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param int           $a my a
     * @param Namespace\Cls $b my b
     */
    public function testParamAnnotation() {
        $parser = new AnnotationParser();
        $reader = new \Doctrine\Common\Annotations\AnnotationReader(null, $parser);

        $annos = $reader->getMethodAnnotations(new \ReflectionMethod('PHPCDI\Util\AnnotationParserTest', 'testParamAnnotation'));

        $this->assertNotNull($annos);
        $this->assertEquals(1, \count($annos));
        $this->assertTrue(\is_array($annos['PHPCDI\Util\PhpDoc\PhpDocParam']));
        $this->assertEquals(2, \count($annos['PHPCDI\Util\PhpDoc\PhpDocParam']));
        $this->assertInstanceOf('PHPCDI\Util\PhpDoc\PhpDocParam', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][0]);
        $this->assertInstanceOf('PHPCDI\Util\PhpDoc\PhpDocParam', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][1]);
        $this->assertEquals('a', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][0]->name);
        $this->assertEquals('int', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][0]->type);
        $this->assertEquals('b', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][1]->name);
        $this->assertEquals('Namespace\Cls', $annos['PHPCDI\Util\PhpDoc\PhpDocParam'][1]->type);
    }

    /**
     * @return Namespace\Cls
     */
    public function testReturnAnnotation() {
        $parser = new AnnotationParser();
        $reader = new \Doctrine\Common\Annotations\AnnotationReader(null, $parser);

        $annos = $reader->getMethodAnnotations(new \ReflectionMethod('PHPCDI\Util\AnnotationParserTest', 'testReturnAnnotation'));

        $this->assertNotNull($annos);
        $this->assertEquals(1, \count($annos));
        $this->assertTrue(isset ($annos['PHPCDI\Util\PhpDoc\PhpDocReturn']));
        $this->assertEquals('Namespace\Cls', $annos['PHPCDI\Util\PhpDoc\PhpDocReturn']->type);
    }
}

