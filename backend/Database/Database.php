<?php

namespace Database;

use PDO;
use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\Insert;
use ClanCats\Hydrahon\Query\Sql\FetchableInterface;

class Database
{
    private static $instance = null;
    private $connection = null;
    private $builder = null;

    private function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";charset=" . DB_CHARSET, DB_USERNAME, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->builder = new Builder('mysql', function ($query, $queryString, $queryParameters) {
                $statement = $this->connection->prepare($queryString);
                $statement->execute($queryParameters);

                if ($query instanceof FetchableInterface) return $statement->fetchAll(PDO::FETCH_ASSOC);
                else if ($query instanceof Insert) return $this->connection->lastInsertId();
                else return $statement->rowCount();
            });
        } catch (\Exception $e) {
            die(var_dump("Database error: " . $e->getMessage(), $e->getTraceAsString()));
        }
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) self::$instance = new self;

        return self::$instance;
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
