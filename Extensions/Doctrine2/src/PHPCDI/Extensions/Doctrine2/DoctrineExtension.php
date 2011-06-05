<?php

namespace PHPCDI\Extensions\Doctrine2;

use PHPCDI\SPI\BeanManager;
use PHPCDI\API\Event\AfterBeanDiscovery;
use PHPCDI\API\DefinitionException;
use PHPCDI\API\Event\ProcessAnnotatedType;

class DoctrineExtension {
    private $repositories = array();
    
    /**
     * @Annos(@Observes @TypeFilter("Doctrine\ORM\EntityRepository") $event)
     */
    public function addUserRepository(ProcessAnnotatedType $event) {
        $entityRepositoryAnnotation = $event->getAnnotatedType()->getAnnotation('PHPCDI\Extensions\Doctrine2\Annotations\EntityRepository');
        
        if($entityRepositoryAnnotation == null) {
            $event->addDefinitionError(new DefinitionException('EntityRepository ' . $event->getAnnotatedType()->getBaseType() . ' must define the @EntityRepository annotation'));
        } else {
            $this->repositories[$entityRepositoryAnnotation->value] = $event->getAnnotatedType()->getBaseType();
        }
    }
    
    /**
     * @Annos(@Observes $event)
     */
    public function toBeans(AfterBeanDiscovery $event, BeanManager $beanManager) {
        foreach ($this->repositories as $entityClass => $repositoryClass) {
            $event->addBean(new RepositoryBean($repositoryClass, $entityClass, $beanManager));
        }
    }
}
