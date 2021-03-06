<?php

namespace PHPCDI\Util;

use PHPCDI\SPI\Annotated;
use PHPCDI\SPI\AnnotatedParameter;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\SPI\AnnotatedField;
use PHPCDI\SPI\AnnotatedCallable;
use PHPCDI\API\Annotations as AnnotationsPkg;
use PHPCDI\API\DefinitionException;

abstract class Annotations {
    public static function getQualifiers(Annotated $annotatedType) {
        if($annotatedType instanceof AnnotatedParameter) {
            $method = $annotatedType->getDeclaringCallable();

            $reader = self::reader();
            $annotations = $reader->getMethodAnnotations($method->getPHPMember());
            $qualifierAnnotations = array();
            foreach($annotations as $anno) {
                if($anno instanceof AnnotationsPkg\P && $anno->name == $annotatedType->getName()) {
                    $data = (array)$anno->value;
                    foreach($data as $obj) {
                        if(self::isQualifier(new \ReflectionClass($obj))) {
                            $qualifierAnnotations[] = $obj;
                        }
                    }
                }
            }
            return $qualifierAnnotations;
        } else  if($annotatedType instanceof AnnotatedType) {
            $class = $annotatedType->getPHPClass();

            $reader = self::reader();
            $annotations = $reader->getClassAnnotations($class);
            $qualifierAnnotations = array();
            foreach($annotations as $anno) {
                if(self::isQualifier(new \ReflectionClass($anno))) {
                    $qualifierAnnotations[] = $anno;
                }
            }
            return $qualifierAnnotations;
        } else if($annotatedType instanceof AnnotatedField) {
            $reader = self::reader();
            $annotations = $reader->getPropertyAnnotations($annotatedType->getPHPMember());
            $qualifierAnnotations = array();
            foreach($annotations as $anno) {
                if(self::isQualifier(new \ReflectionClass($anno))) {
                    $qualifierAnnotations[] = $anno;
                }
            }
            return $qualifierAnnotations;
        } else if($annotatedType instanceof AnnotatedCallable) {
            $reader = self::reader();
            $annotations = $reader->getMethodAnnotations($annotatedType->getPHPMember());
            $qualifierAnnotations = array();
            foreach($annotations as $anno) {
                if(!is_array($anno) && self::isQualifier(new \ReflectionClass($anno))) {
                    $qualifierAnnotations[] = $anno;
                }
            }
            return $qualifierAnnotations;
        } else {
            throw new \Exception('todo');
        }
    }

    public static function isQualifier(\ReflectionClass $annotationClass) {
        $reader = self::reader();
        $annotation = $reader->getClassAnnotation($annotationClass, AnnotationsPkg\Qualifier::className());
        return $annotation != null;
    }

    /**
     * @return \Doctrine\Common\Annotations\AnnotationReader
     */
    public static function reader() {
        if(self::$readerCache == null) {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader(null, new AnnotationParser());
            $reader->setAutoloadAnnotations(true);
            $reader->setDefaultAnnotationNamespace('PHPCDI\API\Annotations\\');
            self::$readerCache = $reader;
        }
        return self::$readerCache;
    }

    private static $readerCache;

    public static function isStereotype(\ReflectionClass $annotationClass) {
        $reader = self::reader();
        $annotation = $reader->getClassAnnotation($annotationClass, AnnotationsPkg\Stereotype::className());
        return $annotation != null;
    }

    public static function getStereotypes(Annotated $annotated) {
        if($annotated instanceof AnnotatedType) {
            $class = $annotated->getPHPClass();

            $reader = self::reader();
            $annotations = $reader->getClassAnnotations($class);
            $stereotypeAnnotations = array();

            foreach($annotations as $anno) {
                if(self::isStereotype(new \ReflectionClass($anno))) {
                    self::getStereotypeHisAnnotations($stereotypeAnnotations, new \ReflectionClass($anno));
                }
            }
            return $stereotypeAnnotations;
        } else if($annotated instanceof AnnotatedCallable) {
            $method = $annotated->getPHPMember();

            $reader = self::reader();
            $annotations = $reader->getMethodAnnotations($method);
            $stereotypeAnnotations = array();

            foreach($annotations as $anno) {
                if(!is_array($anno) && self::isStereotype(new \ReflectionClass($anno))) {
                   self::getStereotypeHisAnnotations($stereotypeAnnotations, new \ReflectionClass($anno));
                }
            }
            return $stereotypeAnnotations;
        } else if($annotated instanceof AnnotatedField) {
            $property = $annotated->getPHPMember();

            $reader = self::reader();
            $annotations = $reader->getPropertyAnnotations($property);
            $stereotypeAnnotations = array();

            foreach($annotations as $anno) {
                if(self::isStereotype(new \ReflectionClass($anno))) {
                    self::getStereotypeHisAnnotations($stereotypeAnnotations, new \ReflectionClass($anno));
                }
            }
            return $stereotypeAnnotations;
        } else {
            throw new Exception('todo');
        }
    }

    private static function getStereotypeHisAnnotations(array &$stereotypeAnnotations, \ReflectionClass $stereotype) {
        $reader = self::reader();
        $annos = $reader->getClassAnnotations($stereotype);
        
        foreach($annos as $anno) {
            if(self::isStereotype(new \ReflectionClass($anno)) && !isset($stereotypeAnnotations[\get_class($anno)])) {
                self::getStereotypeHisAnnotations($stereotypeAnnotations, new \ReflectionClass($anno));
            } else if(!($anno instanceof AnnotationsPkg\Stereotype)) {
                $stereotypeAnnotations[\get_class($anno)] = $anno;
            }
        }
    }

    public static function isScope(\ReflectionClass $annotationClass) {
        $reader = self::reader();
        $scope = $reader->getClassAnnotation($annotationClass, AnnotationsPkg\Scope::className());
        $normalScope = $reader->getClassAnnotation($annotationClass, AnnotationsPkg\NormalScope::className());
        return $scope != null || $normalScope != null;
    }

    /**
     * @param Annotated $annotated
     * @param boolean $useDefault if true this function will never return null
     *
     * @return string Scope annotation
     */
    public static function getScope(Annotated $annotated, $useDefault=true) {
        $reader = self::reader();
        
        $scope = null;

        if($annotated instanceof AnnotatedType) {
            for($class=$annotated->getPHPClass(); $class != null; $class = $class->getParentClass()) {
                $annos = $reader->getClassAnnotations($class);
                foreach($annos as $anno) {
                    if(self::isScope(new \ReflectionClass($anno))) {
                        if($scope != null) {
                            throw new DefinitionException('Bean '.$annotated->getPHPClass()->name.' contains more than one scope annotation');
                        } else {
                            $scope = $anno;
                            break;
                        }
                    }
                }
            }
        } else if($annotated instanceof AnnotatedCallable) {
            $annos = $reader->getMethodAnnotations($annotated->getPHPMember());
            foreach($annos as $anno) {
                if(!is_array($anno) && self::isScope(new \ReflectionClass($anno))) {
                    if($scope != null) {
                        throw new DefinitionException('Producer method '.$annotated->getPHPMember()->name.' of bean '.$annotated->getPHPMember()->class.' contains more than one scope annotation');
                    } else {
                        $scope = $anno;
                        break;
                    }
                }
            }
        } else if($annotated instanceof AnnotatedField) {
            $annos = $reader->getPropertyAnnotations($annotated->getPHPMember());
            foreach($annos as $anno) {
                if(self::isScope(new \ReflectionClass($anno))) {
                    if($scope != null) {
                        throw new DefinitionException('Producer field '.$annotated->getPHPMember()->name.' of bean '.$annotated->getPHPMember()->class.' contains more than one scope annotation');
                    } else {
                        $scope = $anno;
                        break;
                    }
                }
            }
        }

        if($scope == null && $useDefault) {
            $scope = new AnnotationsPkg\Dependent(array());
        } 
        
        if($scope == null) {
            return null;
        } else {
            return \get_class($scope);
        }
    }

    public static function getReturnType(\ReflectionMethod $method) {
        $reader = self::reader();
        $annotation = $reader->getMethodAnnotation($method, 'PHPCDI\Util\PhpDoc\PhpDocReturn');
        return $annotation != null? ReflectionUtil::resolveRelativeClassName($annotation->type, $method->getDeclaringClass()) : null;
    }

    public static function getPropertyType(\ReflectionProperty $property) {
        $reader = self::reader();
        $annotation = $reader->getPropertyAnnotation($property, 'PHPCDI\Util\PhpDoc\PhpDocVar');
        return $annotation != null? $annotation->type : null;
    }
    
    public static function listHasAnnotation($listWithAnnotationObjects, $annotationClass) {
        $found = false;
        
        foreach($listWithAnnotationObjects as $annotation) {
            if(get_class($annotation) == $annotationClass) {
                $found = true;
                break;
            }
        }
        
        return $found;
    }
}
