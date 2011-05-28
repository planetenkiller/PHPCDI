<?php

namespace PHPCDI\Extensions\Doctrine2;

class DoctrineExtension {
    private $repositories = array();
    
    /**
     * @Annos(@Observes @TypeFilter("Doctrine\ORM\EntityRepository") $event)
     */
    public function addUserRepository(\PHPCDI\API\Event\ProcessAnnotatedType $event) {
        $entityRepositoryAnnotation = $event->getAnnotatedType()->getAnnotation('PHPCDI\Extensions\Doctrine2\Annotations\EntityRepository');
        
        if($entityRepositoryAnnotation == null) {
            $event->addDefinitionError(new \PHPCDI\API\DefinitionException('EntityRepository ' . $event->getAnnotatedType()->getBaseType() . ' must define the @EntityRepository annotation'));
        } else {
            $this->repositories[$entityRepositoryAnnotation->value] = $event->getAnnotatedType()->getBaseType();
        }
    }
    
    /**
     * @Annos(@Observes $event)
     */
    public function toBeans(\PHPCDI\API\Event\AfterBeanDiscovery $event, \PHPCDI\API\Inject\SPI\BeanManager $beanManager) {
        foreach ($this->repositories as $entityClass => $repositoryClass) {
            $event->addBean(new RepositoryBean($repositoryClass, $entityClass, $beanManager));
        }
    }
}
