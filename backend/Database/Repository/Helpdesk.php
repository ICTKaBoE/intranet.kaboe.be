<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class Helpdesk extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk", \Database\Object\Helpdesk::class, orderField: 'lastActionTimestamp', orderDirection: 'DESC');
    }

    public function getByUpn($upn)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($upn, $i->creatorUpn));

        return array_values($items);
    }

    public function getByAssignedUpn($upn)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($upn, $i->assignedToUpn));

        return array_values($items);
    }
}
