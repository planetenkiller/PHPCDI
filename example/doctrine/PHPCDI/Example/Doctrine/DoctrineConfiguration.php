<?php

namespace PHPCDI\Example\Doctrine;

class DoctrineConfiguration {
    /**
     * @Produces
     * @PHPCDI\Extensions\Doctrine2\Annotations\ConnectionConfig
     * @ApplicationScoped
     * @return Doctrine\DBAL\Connection
     */
    public function produceConnections() {
        $connectionParams = array(
            'dbname' => 'doctrine_example',
            'memory' => true,
            'driver' => 'pdo_sqlite',
        );
        
        return \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
    }
    
    /**
     * @Produces
     * @ApplicationScoped
     * @return Doctrine\ORM\Configuration
     */
    public function produceConfiguration() {
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__ . '/Entities'));
        $config->setQueryCacheImpl($cache);
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace("PHPCDI\Example\Doctrine\Proxies");
        
        return $config;
    }
    
    /**
     * @Annos(@Observes $event)
     * @param PHPCDI\API\Event\ContainerInitialized $event 
     */
    public function createSchema(\PHPCDI\API\Event\ContainerInitialized $event, \Doctrine\ORM\EntityManager $em) {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }
}

