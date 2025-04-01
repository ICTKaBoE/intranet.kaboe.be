<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Country extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_country", \Database\Object\Country::class, orderField: 'alpha2Code', deletedField: false);
    }

    public function getByAlpha2Code($alpha2Code)
    {
        $statement = $this->prepareSelect();
        $statement->where('alpha2Code', $alpha2Code);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByAlpha3Code($alpha3Code)
    {
        $statement = $this->prepareSelect();
        $statement->where('alpha3Code', $alpha3Code);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByNisCode($nisCode)
    {
        $statement = $this->prepareSelect();
        $statement->where('nisCode', $nisCode);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
