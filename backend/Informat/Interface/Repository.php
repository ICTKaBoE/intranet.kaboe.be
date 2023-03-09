<?php

namespace Informat\Interface;

use Database\Repository\Setting;
use GuzzleHttp\Client;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Repository
{
    public function __construct($endpoint, $schoolyear, $instituteNumber, $object, $idField = 'id', $orderField = 'id', $orderDirection = 'ASC')
    {
        $this->endpoint = $endpoint;
        $this->schoolyear = $schoolyear;
        $this->instituteNumber = $instituteNumber;
        $this->object = $object;
        $this->idField = $idField;
        $this->orderField = $orderField;
        $this->orderDirection = $orderDirection;

        $this->extraGetData = [];
    }

    protected function convertRowsToObject($rows)
    {
        $objects = [];
        foreach ($rows as $row) {
            try {
                if (!empty($row)) $objects[] = $this->convertRowToObject($row);
            } catch (\Exception $e) {
                die(var_dump($e->getMessage()));
            }
        }
        return $objects;
    }

    protected function shiftKeyAndValue($rows)
    {
        $newRows = [];

        foreach ($rows as $row) {
            if (Arrays::hasNestedKey($row, [$this->_shiftKeyAndValue])) {
                $value = Arrays::getValue($row, $this->_shiftKeyAndValue);
                unset($row[$this->_shiftKeyAndValue]);

                if (count($row) == 1) $row = Arrays::first($row);
                if (count($row) == 1) $row = Arrays::first($row);

                foreach ($row as $i => $r) {
                    if (is_array($r)) {
                        $r[$this->_shiftKeyAndValue] = $value;
                        $newRows[] = $r;
                    }
                }
            }
        }

        return $newRows;
    }

    protected function convertRowToObject($row)
    {
        return new $this->object($row);
    }

    protected function setExtraGetData($key, $value)
    {
        $this->extraGetData[$key] = $value;
    }

    public function setInstituteNumber($instituteNumber)
    {
        $this->instituteNumber = $instituteNumber;
    }

    public function setShiftKeyAndValue($key)
    {
        $this->_shiftKeyAndValue = $key;
    }

    public function get($id = null, $order = true)
    {
        $rows = $this->performGet();
        if ($this->_shiftKeyAndValue) $rows = $this->shiftKeyAndValue($rows);

        if ($order && !is_null($this->orderField)) $rows = Strings::equalsIgnoreCase($this->orderDirection, 'asc') ? Arrays::orderBy($rows, $this->orderField) : array_reverse(Arrays::orderBy($rows, $this->orderField));
        if (!is_null($id)) $rows = Arrays::filter($rows, fn ($r) => Strings::equal($r[$this->idField], $id));

        return $this->convertRowsToObject($rows);
    }

    private function performGet()
    {
        $settingsRepo = new Setting;
        $host = rtrim($settingsRepo->get("informat.get.host")[0]->value, "/");
        $username = trim($settingsRepo->get("informat.username")[0]->value);
        $password = trim($settingsRepo->get("informat.password")[0]->value);

        $url = $host . "/{$this->endpoint}";

        $query = [
            'login' => $username,
            'paswoord' => $password,
            'schooljaar' => $this->schoolyear,
            'instelnr' => $this->instituteNumber
        ];

        foreach ($this->extraGetData as $key => $value) $query[$key] = $value;
        $url .= "?" . http_build_query($query);

        try {
            $client = new Client();
            $response = $client->get($url, [
                'headers' => ['Accept' => 'application/xml'],
            ]);

            $body = $response->getBody()->getContents();
            $xml = simplexml_load_string($body);
            $json = json_encode($xml);
            $array = json_decode($json, true);

            return count($array) !== 1 ? $array : Arrays::first($array);
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getTraceAsString();
        }
    }
}
