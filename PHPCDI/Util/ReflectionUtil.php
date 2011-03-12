<?php

namespace PHPCDI\Util;

abstract class ReflectionUtil {
    public static function getClassNames(\ReflectionClass $reflectionClass) {
        $store = array();

        for($class=$reflectionClass; $class != null; $class = $class->getParentClass()) {
            $store[] = $class->name;
            foreach($class->getInterfaces() as $interface) {
                $store[] = $interface->name;
                for($extendedInterface=$interface->getParentClass(); $extendedInterface != null; $extendedInterface=$extendedInterface->getParentClass()) {
                    $store[] = $extendedInterface->name;
                }
            }
        }

        return \array_unique($store);
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
                           ? Annotations::reader()->getMethodAnnotation($reflectionClass->getConstructor(), 'PHPCDI\API\Inject\Inject') != null
                           : false;
        $hasParameter = $reflectionClass->getConstructor() != null
                            ? $reflectionClass->getConstructor()->getNumberOfParameters() > 0
                            : false;

        return !$isAbstract && !$isAnnotation && (!$hasParameter || ($hasParameter && $hasAtInject));
    }
}
