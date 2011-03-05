<?php

namespace PHPCDI\Util;

use PHPCDI\API\Inject\SPI\Bean;
use PHPCDI\API\Inject\SPI\AnnotatedCallable;
use PHPCDI\API\Inject\SPI\AnnotatedConstructor;
use PHPCDI\Injection\ParameterInjectionPoint;
use PHPCDI\API\Inject\SPI\AnnotatedType;
use PHPCDI\API\Context\SPI\CreationalContext;

/**
 *
 */
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
        } else if($constructor->isAnnotationPresent('PHPCDI\API\Inject\Inject')) {
            $injectionPoints = array();
            foreach($constructor->getParameters() as $parameter) {
                $injectionPoints[] = new ParameterInjectionPoint($declaringBean, $parameter);
            }

            return $injectionPoints;
        } else if(\count($constructor->getParameters()) > 0) {
            throw new \PHPCDI\API\Inject\DefinitionException('Found non @Inject constructor with at leas one paramter');
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
                && $method->isAnnotationPresent('PHPCDI\API\Inject\Inject')) { //TODO: not Produces,Disposes,Observes
                $initializerMethodsList[] = $method;
            }
        }

        return $method;
    }

    public static function getFieldInjectionPoints(Bean $declaringBean, AnnotatedType $class) {
        $fields = array();

        //TODO check superclass too
        foreach($class->getFields() as $field) {
            if(!$field->isStatic() && $field->isAnnotationPresent('PHPCDI\API\Inject\Inject')) { //TODO: not producer
                $fields[] = new \PHPCDI\Injection\FieldInjectionPoint($declaringBean, $field);
            }
        }

        return $fields;
    }

    public static function injectFieldsAndInitializers($obj, CreationalContext $ctx, \PHPCDI\Bean\BeanManager $mgr, array $injectableFields, array $initializerMethods) {
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
            if($method->isAnnotationPresent('PHPCDI\API\Inject\PostConstruct')) {
                $methods[] = $method;
            }
        }

        //TODO only one postconstruct per (super)class
        if(count($methods) > 1) {
            throw new \PHPCDI\API\Inject\DefinitionException('too many post construcct methods in class '.$class->getPHPClass()->name);
        }

        return $methods;
    }
}
