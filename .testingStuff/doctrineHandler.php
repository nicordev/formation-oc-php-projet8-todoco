<?php

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var SchemaTool
     */
    private $schemaTool;

    public function __construct(string $srcDirectory = "", array $connectionParams = [
        'dbname' => 'ocp8_test',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql'
    ]) {
        if (empty($srcDirectory)) {
            $srcDirectory = __DIR__ . "/../../src";
        }

        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(
            [$srcDirectory],
            $isDevMode,
            $proxyDir,
            $cache,
            $useSimpleAnnotationReader
        );

        $this->entityManager = EntityManager::create($connectionParams, $config);
        $this->schemaTool = new SchemaTool($this->entityManager);
    }

    public function createSchema(array $entityClasses)
    {
        $this->schemaTool->createSchema($this->getClassesMetadata($entityClasses));
    }

    public function dropSchema(array $entityClasses)
    {
        $this->schemaTool->dropSchema($this->getClassesMetadata($entityClasses));
    }

    public function updateSchema(array $entityClasses)
    {
        $this->schemaTool->updateSchema($this->getClassesMetadata($entityClasses));
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    private function getClassesMetadata(array $entityClasses)
    {
        $classes = [];

        foreach ($entityClasses as $entityClass) {
            $classes[] = $this->entityManager->getClassMetadata($entityClass);
        }

        return $classes;
    }
}