<?php

namespace PHPCDI\Util;

use PHPCDI\SPI\Bean;
use PHPCDI\SPI\AnnotatedCallable;
use PHPCDI\SPI\AnnotatedConstructor;
use PHPCDI\Injection\ParameterInjectionPoint;
use PHPCDI\SPI\AnnotatedType;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\API\Annotations as AnnotationsPkg;
use PHPCDI\API\DefinitionException;
use PHPCDI\Injection\FieldInjectionPoint;
use PHPCDI\Manager\BeanManager;

abstract class Beans {
    public static function getParameterInjectionPoints(Bean $declaringBean, AnnotatedCallable $constructor)
    {
        $injectionPoints = array();
        foreach($constructor->getParameters() as $parameter) {
            $injectionPoints[] = new ParameterInjectionPoint($declaringBean, $parameter);
        }

        return $injectionPoints;
    }

    /**
     *
     * @param Bean $declaringBean
     * @param $this->reflectionClass->getConstructor() $constructor
     * 
     * @return ParameterInjectionPoint
     */
    public static function getParameterInjectionPointsOfConstructor(Bean $declaringBean, $constructor)
    {
        if($constructor == null) {
            return array();
        } else if($constructor->isAnnotationPresent(AnnotationsPkg\Inject::className())) {
            $injectionPoints = array();
            foreach($constructor->getParameters() as $parameter) {
                $injectionPoints[] = new ParameterInjectionPoint($declaringBean, $parameter);
            }

            return $injectionPoints;
        } else if(\count($constructor->getParameters()) > 0) {
            throw new DefinitionException('Found non @Inject constructor with at leas one paramter');
        } else {
            return array();
        }
    }
 
    public static function getInitializerMethods(Bean $declaringBean, AnnotatedType $class)
    {
        $initializerMethodsList = array();

        //TODO check superclass too
        foreach($class->getMethods() as $method) {
            if(!$method->isStatic()
                && $method->isAnnotationPresent(AnnotationsPkg\Inject::className())) { //TODO: not Produces,Disposes,Observes
                $initializerMethodsList[] = $method;
            }
        }

        return $initializerMethodsList;
    }

    public static function getFieldInjectionPoints(Bean $declaringBean, AnnotatedType $class) {
        $fields = array();

        //TODO check superclass too
        foreach($class->getFields() as $field) {
            if(!$field->isStatic() && $field->isAnnotationPresent(AnnotationsPkg\Inject::className())) { //TODO: not producer
                $fields[] = new FieldInjectionPoint($declaringBean, $field);
            }
        }

        return $fields;
    }

    public static function injectFieldsAndInitializers($obj, CreationalContext $ctx, BeanManager $mgr, array $injectableFields, array $initializerMethods) {
        foreach($injectableFields as $field) {
            $field->inject($obj, $mgr, $ctx);
        }

        foreach($initializerMethods as $method) {
            $values = array();
            foreach ($method['injectionpoints'] as $injection) {
                $values[] = $mgr->getInjectableReference($injection, $ctx);
            }

            $reflectionMethod = $method['method']->getPHPMember();
            $reflectionMethod->invokeArgs($obj, $values);
        }
    }

    public static function getPostConstructMethods(AnnotatedType $class) {
        $methods = array();

        //TODO check superclass too
        foreach($class->getMethods() as $method) {
            if($method->isAnnotationPresent(AnnotationsPkg\PostConstruct::className())) {
                $methods[] = $method;
            }
        }

        //TODO only one postconstruct per (super)class
        if(count($methods) > 1) {
            throw new DefinitionException('too many post construcct methods in class '.$class->getPHPClass()->name);
        }

        return $methods;
    }
    
    public static function compareQualifiers(array $a, array $b) {
        foreach($b as $qualifier) {
            foreach($a as $anno) {
                if(self::compareQualifier($qualifier, $anno)) {
                    continue 2;
                }
            }

            return false;
        }
        
        return true;
    }
    
    public static function compareQualifier($a, $b) {
        $aObj = \is_object($a)? $a : new $a(array());
        $bObj = \is_object($b)? $b : new $b(array());
        
        return ($aObj == $bObj); // compared class and class attribute values
    }
    
    public static function mergeQualifiers(array $current, array $new) {
        $checkedQualifiers = array();
        foreach($new as $qualifier) {
            $cls = new \ReflectionClass($qualifier);
            if(!Annotations::isQualifier($cls)) {
                throw new \InvalidArgumentException('Annotation ' . $cls->name . ' is not an qualifier');
            } else if(isset($checkedQualifiers[$cls->name])) {
                throw new \InvalidArgumentException('Redundant qualifiers: ' . $cls->name);
            }
            
            $checkedQualifiers[$cls->name] = \is_string($qualifier)? $cls->newInstance(array()) : $qualifier;
        }
        
        return \array_merge($checkedQualifiers, $current);
    }
    
    public static function containsAllQualifiers(array $requiredQualifiers, array $qualifiers) {
        
        foreach($requiredQualifiers as $requiredQualifier) {
            $matchFound = false;
            foreach($qualifiers as $qualifier) {
                if(self::compareQualifier($requiredQualifier, $qualifier)) {
                    $matchFound = true;
                    break;
                }
            }
            
            if(!$matchFound) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function toString(array $beans) {
        $string = '[';
        
        foreach($beans as $bean) {
            $string .= $bean->__toString() . ', ';
        }
        
        $string .= ']';
        return $string;
    }
}
