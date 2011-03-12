<?php

namespace PHPCDI\API\Inject\SPI;

/**
 *
 */
interface BeanManager {
    /**
     * @param Bean $bean
     * @param string $beanType
     * @param \PHPCDI\API\Context\SPI\CreationalContext $ctx
     */
    public function getRefernce(\PHPCDI\API\Inject\SPI\Bean $bean, $beanType, \PHPCDI\API\Context\SPI\CreationalContext $ctx);

    /**
     * @param InjectionPoint $ij
     * @param \PHPCDI\API\Context\SPI\CreationalContext $ctx
     */
    public function getInjectableReference($ij, $ctx);

    /**
     * @param \PHPCDI\API\Context\SPI\Contextual $contextual
     *
     * @return \PHPCDI\API\Context\SPI\CreationalContext
     */
    public function createCreationalContext($contextual);

    /**
     * @return array of Bean
     */
    public function getBeans($beanType, $qualifiers);

    public function resolve($beans);

    /**
     * @param InjectionPoint $injectionPoint
     */
    public function validate($injectionPoint);

    public function isScope($annotationType);

    public function isNormalScope($annotationType);

    public function isQualifier($annotationType);

    public function isStereotype($annotationType);

    public function getStereotypeDefinition($stereotypeAnnotation);

    /**
     * @return \PHPCDI\API\Context\SPI\Context
     */
    public function getContext($scopeType);

    /**
     * @return AnnotatedType
     */
    public function createAnnotatedType($class);

    /**
     * @param AnnotatedType $annotatedType
     * 
     * @return InjectionTarget
     */
    public function createInjectionTarget($annotatedType);
    
    public function fireEvent($eventData, array $qualifiers);
    
    /**
     * @return array of ObserverMethod
     */
    public function resolveObserverMethods($eventData, array $qualifiers);
}
