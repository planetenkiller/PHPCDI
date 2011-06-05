<?php

namespace PHPCDI\SPI;

use PHPCDI\SPI\Bean;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\SPI\InjectionPoint;
use PHPCDI\SPI\Context\Contextual;

interface BeanManager {
    public function getRefernce(Bean $bean, $beanType, CreationalContext $ctx);

    public function getInjectableReference(InjectionPoint $ij, CreationalContext $ctx);

    /**
     * @param \PHPCDI\SPI\Context\Contextual $contextual
     *
     * @return \PHPCDI\API\Context\SPI\CreationalContext
     */
    public function createCreationalContext(Contextual $contextual);

    /**
     * @return \PHPCDI\SPI\Bean[] beans
     */
    public function getBeans($beanType, $qualifiers);

    public function resolve($beans);

    public function validate(InjectionPoint $injectionPoint);

    public function isScope($annotationType);

    public function isNormalScope($annotationType);

    public function isQualifier($annotationType);

    public function isStereotype($annotationType);

    public function getStereotypeDefinition($stereotypeAnnotation);

    /**
     * @return \PHPCDI\SPI\Context\Context
     */
    public function getContext($scopeType);

    /**
     * @return \PHPCDI\SPI\AnnotatedType
     */
    public function createAnnotatedType($class);

    /**
     * @param \PHPCDI\SPI\AnnotatedType $annotatedType
     * 
     * @return \PHPCDI\SPI\InjectionTarget
     */
    public function createInjectionTarget($annotatedType);
    
    public function fireEvent($eventData, array $qualifiers);
    
    /**
     * @return \PHPCDI\SPI\ObserverMethod
     */
    public function resolveObserverMethods($eventData, array $qualifiers);
}
