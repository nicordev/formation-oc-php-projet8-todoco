<?php

namespace Tests\TestHelper;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineHandler
{
    private static $instance;
    private $entityManager;
    /**
     * @var bool
     */
    private $isDevMode;
    /**
     * @var null
     */
    private $proxyDirectory;
    /**
     * @var null
     */
    private $cache;
    /**
     * @var bool
     */
    private $useSimpleAnnotationReader;

    private function __construct()
    {}

    public static function getInstance(
        $isDevMode = true,
        $proxyDirectory = null,
        $cache = null,
        $useSimpleAnnotationReader = false
    ) {
        if(is_null(self::$instance)) {
            self::$instance = new DoctrineHandler();
            self::$instance->isDevMode = $isDevMode;
            self::$instance->proxyDirectory = $proxyDirectory;
            self::$instance->cache = $cache;
            self::$instance->useSimpleAnnotationReader = $useSimpleAnnotationReader;
        }

        return self::$instance;
    }

    public function makeEntityManager(
        string $srcPath,
        string $databaseName,
        string $user = "root",
        string $password = "",
        string $driver = "pdo_mysql"
    ) {
        if (!$this->entityManager) {
            $config = Setup::createAnnotationMetadataConfiguration(
                [$srcPath],
                $this->isDevMode,
                $this->proxyDirectory,
                $this->cache,
                $this->useSimpleAnnotationReader
            );

            $this->entityManager = EntityManager::create([
                "driver" => $driver,
                "dbname" => $databaseName,
                "user" => $user,
                "password" => $password
            ], $config);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
