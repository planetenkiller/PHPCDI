<?php

namespace PHPCDI\Extensions\Doctrine2;

/**
 * Contains doctrine related producers. 
 */
class DoctrineProducer {
    /**
     * Produces a doctrine entity manager.
     * 
     * @Annos(@PHPCDI\Extensions\Doctrine2\Annotations\ConnectionConfig $connection)
     * 
     * @param mixed $connection
     * @param Doctrine\ORM\Configuration $config 
     * 
     * @Produces
     * @ApplicationScoped
     * @return Doctrine\ORM\EntityManager
     */
    public function produceEntityManager($connection, \Doctrine\ORM\Configuration $config) {
        return \Doctrine\ORM\EntityManager::create($connection, $config);
    }
    
    /**
     * Produces an doctrine entity repository.
     * 
     * @param Doctrine\ORM\EntityManager $em 
     * 
     * @Produces
     * @return Doctrine\ORM\EntityRepository
     */
    public function produceRepository(\Doctrine\ORM\EntityManager $em, \PHPCDI\API\Inject\SPI\InjectionPoint $ij) {
        $entityRepositoryAnnotation = $ij->getAnnotated()->getAnnotation('PHPCDI\Extensions\Doctrine2\Annotations\EntityRepository');
        
        if($entityRepositoryAnnotation != null) {
            return $em->getRepository($entityRepositoryAnnotation->value);
        } else {
            throw new \PHPCDI\API\DefinitionException('Injection point [' . $ij . '] must define the @EntityRepository annotation');
        }
    }
}

