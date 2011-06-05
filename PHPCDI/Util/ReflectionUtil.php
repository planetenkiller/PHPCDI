<?php

namespace PHPCDI\Util;

use PHPCDI\API\Annotations as AnnotationsPkg;

abstract class ReflectionUtil {
    private static $PRIMITIVE_TYPES = array('bool', 'boolean', 'int', 'integer', 'float', 'string', 'array', 'mixed', 'object');
    private static $PRIMITIVE_TYPE_ALIASES = array('bool' => 'boolean',
                                                   'boolean' => 'bool',
                                                   'int' => 'integer',
                                                   'integer' => 'int',
                                                   'mixed' => 'object',
                                                   'object' => 'mixed');


    public static function isPrimitiveType($typeName) {
        return in_array($typeName, self::$PRIMITIVE_TYPES);
    }
    
    public static function getClassNames($className) {
        $store = array();

        if(!($className instanceof \ReflectionClass) && in_array($className, self::$PRIMITIVE_TYPES)) {
            if($className != 'mixed') {
                $types = array($className, 'mixed');
                
                if(isset(self::$PRIMITIVE_TYPE_ALIASES[$className])) {
                    $types[] = self::$PRIMITIVE_TYPE_ALIASES[$className];
                }
                
                return $types;
            } else {
                return 'mixed';
            }
        } else {
            $reflectionClass = ($className instanceof \ReflectionClass)? $className : new \ReflectionClass($className);
            for($class=$reflectionClass; $class != null; $class = $class->getParentClass()) {
                $store[] = $class->name;
                foreach($class->getInterfaces() as $interface) {
                    $store[] = $interface->name;
                    for($extendedInterface=$interface->getParentClass(); $extendedInterface != null; $extendedInterface=$extendedInterface->getParentClass()) {
                        $store[] = $extendedInterface->name;
                    }
                }
            }

            $store[] = 'mixed';

            return \array_unique($store);
        }
    }
    
    public static function getAllFields(\ReflectionClass $reflectionClass) {
        $store = array();

        for($class=$reflectionClass; $class != null; $class = $class->getParentClass()) {
            foreach($class->getProperties() as $property) {
                if(!$property->isStatic()) {
                    $store[] = $property;
                }
            }
        }

        return $store;
    }

    public static function getAllMethods(\ReflectionClass $reflectionClass) {
        $store = array();

        $overwrittenMethods = array();

        for($class=$reflectionClass; $class != null; $class = $class->getParentClass()) {
            foreach($class->getMethods() as $method) {
                if(\substr($method->name, 0, 2) !== '__' &&
                        !$method->isStatic()
                        && !\in_array($method->class."::".$method->name, $overwrittenMethods)) {
                    try {
                        $origMethod = $method->getPrototype();
                        $overwrittenMethods[] = $origMethod->class."::".$origMethod->name;
                    } catch (\ReflectionException $e) {
                        
                    }

                    $store[] = $method;
                }
            }
        }

        return $store;
    }

    public static function isManagedBean(\ReflectionClass $reflectionClass) {
        $isAbstract = $reflectionClass->isAbstract() || $reflectionClass->isInterface();
        $isAnnotation = \in_array('Doctrine\Common\Annotations\Annotation', \class_parents($reflectionClass->name));
        $hasAtInject = $reflectionClass->getConstructor() != null
                           ? Annotations::reader()->getMethodAnnotation($reflectionClass->getConstructor(), AnnotationsPkg\Inject::className()) != null
                           : false;
        $hasParameter = $reflectionClass->getConstructor() != null
                            ? $reflectionClass->getConstructor()->getNumberOfParameters() > 0
                            : false;

        return !$isAbstract && !$isAnnotation && (!$hasParameter || ($hasParameter && $hasAtInject));
    }
    
    public static function decapitalize($name) {
	if (empty($name)) {
	    return $name;
	}
	if (strlen($name) > 1 
            && strtoupper($name[1]) == $name[1] 
            && strtoupper($name[0]) == $name[0]){
	    return $name;
	}
	
        $name[0] = strtolower($name[0]);
        return $name;
    }
    
    public static function resolveRelativeClassName($className, \ReflectionClass $classOfFile) {
        if(!self::isPrimitiveType($className) && !class_exists($className) && !interface_exists($className)) {
            return $classOfFile->getNamespaceName() . '\\' . $className;
        } else {
            return $className;
        }
    }
}
