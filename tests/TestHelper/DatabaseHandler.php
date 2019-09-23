<?php

namespace Tests\TestHelper;

class DatabaseHandler
{
    /**
     * @var DatabaseHandler
     */
    private static $instance;
    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var int
     */
    private $fetchStyle = \PDO::FETCH_ASSOC;

    private function __construct()
    {}

    /**
     * Get the single instance of the database handler
     *
     * @return DatabaseHandler
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new DatabaseHandler();
        }

        return self::$instance;
    }

    /**
     * Connect to a mysql database with \PDO
     *
     * @param string $database
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $driver
     * @param string $charset
     * @return DatabaseHandler
     */
    public function connect(
        string $database,
        string $host = "localhost",
        string $user = "root",
        string $password = "",
        string $charset = "utf8",
        string $driver = "mysql"
    ) {
        if (!$this->pdo) {
            $dsn = "$driver:host=$host;dbname=$database;charset=$charset";
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this;
    }

    /**
     * Execute a SQL query and return the PDOStatement
     *
     * @param string $sqlQuery
     * @param array|null $propertiesAndValues
     * @return bool|false|PDOStatement
     */
    public function query(string $sqlQuery, ?array $propertiesAndValues = null)
    {
        if ($propertiesAndValues) {
            $statement = $this->pdo->prepare($sqlQuery);
            return $statement->execute($propertiesAndValues);
        }

        return $this->pdo->query($sqlQuery);
    }

    /**
     * Select rows
     *
     * @param string $table
     * @param string $select
     * @param string $where
     * @return bool|false|PDOStatement
     */
    public function select(string $table, string $select = "*", ?string $where = null)
    {
        if ($where) {
            return $this->query("SELECT {$select} FROM {$table} WHERE {$where}");
        }

        return $this->query("SELECT {$select} FROM {$table}");
    }

    /**
     * Fetch a row from a PDOStatement
     *
     * @param PDOStatement $statement
     * @param string|null $entityClass
     * @return mixed
     */
    public function fetch(\PDOStatement $statement, ?string $entityClass = null)
    {
        if ($entityClass) {
            $statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $entityClass);
            return $statement->fetch();
        }
        return $statement->fetch($this->fetchStyle);
    }

    /**
     * Fetch all rows from a PDOStatement
     *
     * @param PDOStatement $statement
     * @param string|null $entityClass
     * @return array
     */
    public function fetchAll(\PDOStatement $statement, ?string $entityClass = null)
    {
        if ($entityClass) {
            $statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $entityClass);

            return $statement->fetchAll();
        }

        return $statement->fetchAll($this->fetchStyle);
    }

    /**
     * Find a row
     *
     * @param string $table
     * @param array $criteria
     * @param string|null $entityClass
     * @param string $select
     * @return mixed
     */
    public function find(string $table, array $criteria = ["id" => 1], ?string $entityClass = null, string $select = "*")
    {
        $property = array_key_first($criteria);
        $value = $criteria[$property];
        if (is_string($value)) {
            $value = "'{$value}'";
        }
        $statement = $this->select($table, $select, "{$property} = {$value}");

        return $this->fetch($statement, $entityClass);
    }

    /**
     * Find all rows
     *
     * @param string $table
     * @param string|null $entityClass
     * @param string|null $where
     * @param string $select
     * @return array
     */
    public function findAll(string $table, ?string $entityClass = null, ?string $where = null, string $select = "*")
    {
        $statement = $this->select($table, $select, $where);

        return $this->fetchAll($statement, $entityClass);
    }

    /**
     * Delete some rows in a table
     *
     * @param string $table
     * @param string $where
     * @return bool|false|PDOStatement
     */
    public function delete(string $table, string $where)
    {
        return $this->query("DELETE FROM {$table} WHERE {$where}");
    }

    /**
     * Delete all rows of a table
     *
     * @param string $table
     * @return bool|false|PDOStatement
     */
    public function purgeTable(string $table)
    {
        return $this->query("DELETE FROM {$table}");
    }

    /**
     * Insert a row
     *
     * @param string $table
     * @param array $propertiesAndValues
     * @return bool|false|PDOStatement
     */
    public function insertARow(string $table, array $propertiesAndValues)
    {
        $properties = array_keys($propertiesAndValues);
        $columns = implode(", ", $properties);
        $interrogationPoints = [];

        foreach ($properties as $property) {
            $interrogationPoints[] = "?";
        }

        $interrogationPoints = implode(", ", $interrogationPoints);
        $sqlQuery = "INSERT INTO {$table}({$columns}) VALUES ({$interrogationPoints})";

        return $this->query($sqlQuery, array_values($propertiesAndValues));
    }

    /**
     * Insert a row from an entity
     *
     * @param string $table
     * @param $entity
     * @return bool|false|PDOStatement
     */
    public function insertEntity(string $table, $entity)
    {
        $reflection = new \ReflectionObject($entity);
        $properties = $reflection->getProperties();
        $columns = $this->getColumnNames($table);
        $propertiesAndValues = [];

        foreach ($properties as $property) {
            $propertyName = $property->name;

            if (in_array($propertyName, $columns)) {
                if ($property->isPrivate()) {
                    $property->setAccessible(true);
                }
                $propertiesAndValues[$propertyName] = $property->getValue($entity);
            }
        }

        return $this->insertARow($table, $propertiesAndValues);
    }

    /**
     * Get the column names of a table
     *
     * @param string $table
     * @return array
     */
    public function getColumnNames(string $table)
    {
        $columns = $this->getColumns($table);
        $names = [];

        foreach ($columns as $column) {
            $names[] = reset($column);
        }

        return $names;
    }

    /**
     * Get the column properties of a table
     *
     * @param string $table
     * @return array
     */
    public function getColumns(string $table)
    {
        return $this->fetchAll(
            $this->query("SHOW COLUMNS FROM $table"),
            \PDO::FETCH_ASSOC
        );
    }

    /**
     * Check if a column exists in a table
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function isColumn(string $table, string $column)
    {
        return is_array(
            $this->fetch(
                $this->query("SHOW COLUMNS FROM $table LIKE '$column'"),
                \PDO::FETCH_NUM
            )
        );
    }

    /**
     * Guess the table name from the entity class name
     *
     * @param $entity
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getTableNameFromEntity($entity)
    {
        $fullClassName = get_class($entity);
        $classNameParts = explode("\\", $fullClassName);
        $table = strtolower(end($classNameParts));
        $query = "SHOW TABLES LIKE '{$table}'";
        $request = $this->pdo->query($query);
        $result = $request->fetch(\PDO::FETCH_NUM);

        if ($result) {
            return $table;
        }

        throw new \InvalidArgumentException("No table has been found for the entity {$fullClassName}.");
    }

    /**
     * @return int
     */
    public function getFetchStyle(): int
    {
        return $this->fetchStyle;
    }

    /**
     * @param int $fetchStyle
     * @return DatabaseHandler
     */
    public function setFetchStyle(int $fetchStyle): DatabaseHandler
    {
        $this->fetchStyle = $fetchStyle;

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @param \PDO $pdo
     * @return DatabaseHandler
     */
    public function setPdo(\PDO $pdo): DatabaseHandler
    {
        $this->pdo = $pdo;

        return $this;
    }
}
