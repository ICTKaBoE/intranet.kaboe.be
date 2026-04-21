<?php

namespace Smartschool\Repository;

use Smartschool\Interface\Repository;

class AllAccountsExtended extends Repository
{
    public function __construct($sourceId)
    {
        parent::__construct($sourceId, "getAllAccountsExtended", \Smartschool\Object\AllAccountsExtended::class, [self::OUTPUT_JSON, self::OUTPUT_ARRAY]);
    }
}
