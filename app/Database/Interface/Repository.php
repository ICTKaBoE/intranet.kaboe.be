<?php

namespace Database\Interface;

use Database\Database;
use Ouzo\Utilities\Arrays;

class Repository
{
    public function __construct($table, $object, $idField = 'id', $orderField = 'order', $orderDirection = 'ASC', $deletedField = 'deleted')
    {
        $this->db = new Database;
        $this->table = $table;
        $this->object = $object;
        $this->idField = $idField;
        $this->orderField = $orderField;
        $this->orderDirection = $orderDirection;
        $this->deletedField = $deletedField;

        $this->repoTable = $this->db->builder->table($this->table);
    }

    protected function convertRowsToObject($rows)
    {
        $objects = [];
        foreach ($rows as $row) $objects[] = $this->convertRowToObject($row);

        return $objects;
    }

    protected function convertRowToObject($row)
    {
        return new $this->object($row);
    }

    public function get($id = null, $order = true, $deleted = false)
    {
        $statement = $this->repoTable->select();
        if (!is_null($id)) $statement->where($this->idField, $id);
        if ($this->deletedField && !$deleted) $statement->where($this->deletedField, "0");
        if ($order && $this->orderField) $statement->orderBy($this->orderField, $this->orderDirection);

        $rows = $statement->get();
        return $this->convertRowsToObject($rows);
    }

    public function set($object)
    {
        $object = $object->toArray();
        if (is_null($object[$this->idField])) return $this->insert($object);
        else return $this->update($object);
    }

    public function update($object)
    {
        $id = $object[$this->idField];
        unset($object[$this->idField]);

        $statement = $this->repoTable->update($object)->where($this->idField, $id);
        return $statement->execute();
    }

    public function insert($object)
    {
        unset($object[$this->idField]);
        unset($object[$this->deletedField]);

        foreach ($object as $key => $value) {
            if (is_null($value)) unset($object[$key]);
        }

        $statement = $this->repoTable->insert($object);
        return $statement->execute();
    }
}
