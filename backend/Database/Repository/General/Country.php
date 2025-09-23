<?php

namespace Database\Repository\General;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Country extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_country", \Database\Object\General\Country::class, orderField: 'alpha2Code', deletedField: false);
    }

    public function getByAlpha2Code($alpha2Code)
    {
        $statement = $this->prepareSelect(filters: ['alpha2Code' => $alpha2Code]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByAlpha3Code($alpha3Code)
    {
        $statement = $this->prepareSelect(filters: ['alpha3Code' => $alpha3Code]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByNisCode($nisCode)
    {
        $statement = $this->prepareSelect(filters: ['nisCode' => $nisCode]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ['name' => $name]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByNationality($nationality)
    {
        $statement = $this->prepareSelect(filters: ['nationality' => $nationality]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
