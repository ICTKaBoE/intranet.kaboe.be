<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class HelpdeskMessage extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_message", \Database\Object\HelpdeskMessage::class, orderField: 'timestamp', orderDirection: 'DESC');
    }

    public function getByHelpdeskId($helpdeskId)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($helpdeskId, $i->helpdeskId));

        return array_values($items);
    }
}
