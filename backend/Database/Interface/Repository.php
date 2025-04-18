<?php

namespace Database\Interface;

use Database\Database;
use Database\Interface\CustomObject;
use Ouzo\Utilities\Arrays;
use Security\GUID;
use stdClass;

class Repository extends stdClass
{
    public function __construct($table, $object, $idField = 'id', $orderField = 'order', $orderDirection = 'ASC', $deletedField = 'deleted', $guidField = 'guid')
    {
        $this->db = Database::getInstance();
        $this->table = $table;
        $this->object = $object;
        $this->idField = $idField;
        $this->orderField = $orderField;
        $this->orderDirection = $orderDirection;
        $this->deletedField = $deletedField;
        $this->guidField = $guidField;

        $this->repoTable = $this->db->getBuilder()->table($this->table);
    }

    protected function prepareSelect($id = null, $order = true, $deleted = false)
    {
        $statement = $this->repoTable->select();
        if (!is_null($id)) {
            if ($this->guidField) {
                if (strlen($id) > 12) $statement->where($this->guidField, $id);
                else $statement->where($this->idField, $id);
            } else $statement->where($this->idField, $id);
        }
        if ($this->deletedField && !$deleted) $statement->where($this->deletedField, "0");
        if ($order && $this->orderField) $statement->orderBy($this->orderField, $this->orderDirection);

        return $statement;
    }

    protected function executeSelect($statement)
    {
        $objects = [];
        $rows = $statement->get();

        foreach ($rows as $row) $objects[] = new $this->object($row);

        return $objects;
    }

    public function get($id = null, $order = true, $deleted = false)
    {
        $statement = $this->prepareSelect($id, $order, $deleted);
        return $this->executeSelect($statement);
    }

    public function set(CustomObject $object, $only = [])
    {
        try {
            $object = $object->toSqlArray();

            if (is_null($object[$this->idField])) return $this->insert($object);
            else return $this->update($object, $only);
        } catch (\Exception $e) {
            die(var_dump("Repository:set - " . $e->getMessage()));
        }
    }

    public function update($object, $only = [])
    {
        $id = $object[$this->idField];
        unset($object[$this->idField]);

        if (count($only)) {
            foreach ($object as $key => $value) {
                if (!in_array($key, $only)) unset($object[$key]);
            }
        }

        $statement = $this->repoTable->update($object)->where($this->idField, $id);
        return $statement->execute();
    }

    public function insert($object)
    {
        unset($object[$this->idField]);
        unset($object[$this->deletedField]);

        if ($this->guidField) $object[$this->guidField] = $object[$this->guidField] ?: GUID::create();

        foreach ($object as $key => $value) {
            if (is_null($value)) unset($object[$key]);
        }

        $statement = $this->repoTable->insert($object);
        return $statement->execute();
    }

    public function delete($params = [])
    {
        $statement = $this->repoTable->delete();

        foreach ($params as $key => $value) $statement->where($key, $value);

        $statement->execute();
    }

    public function deleteWhereDeleteTrue()
    {
        $this->repoTable->delete()->where("deleted", 1)->execute();
    }
}
