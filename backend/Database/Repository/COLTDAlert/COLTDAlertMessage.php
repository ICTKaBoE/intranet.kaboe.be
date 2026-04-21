<?php

namespace Database\Repository\COLTDAlert;

use Database\Interface\Repository;

class COLTDAlertMessage extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_coltdalert_message", \Database\Object\COLTDAlert\COLTDAlertMessage::class, orderField: "to", deletedField: false);
    }

    public function getByColtdalertId($coltdalertId)
    {
        $statement = $this->prepareSelect(filters: ['coltdalertId' => $coltdalertId]);
        return $this->executeSelect($statement);
    }
}
