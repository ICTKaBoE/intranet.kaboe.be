<?php

namespace Database;

use PDO;
use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\Insert;
use ClanCats\Hydrahon\Query\Sql\FetchableInterface;

class Database
{
    public function __construct()
    {
        try {
            $connection = new PDO("mysql:host=ID75803_intranet.db.webhosting.be;dbname=ID75803_intranet", "ID75803_intranet", "PianomanPA1");

            $this->builder = new Builder('mysql', function ($query, $queryString, $queryParameters) use ($connection) {
                $statement = $connection->prepare($queryString);
                $statement->execute($queryParameters);

                if ($query instanceof FetchableInterface) return $statement->fetchAll(PDO::FETCH_ASSOC);
                else if ($query instanceof Insert) return $connection->lastInsertId();
                else return $statement->rowCount();
            });
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }
}
