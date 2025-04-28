<?php

namespace Database\Repository\Signage;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Playlist extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_signage_playlist", \Database\Object\Signage\Playlist::class, orderField: 'name');
    }

    public function getByAssignedToAndAssignedToId($assignedTo, $assignedToId)
    {
        $statement = $this->prepareSelect(filters: [
            'assignedTo' => $assignedTo,
            'assignedToId' => $assignedToId
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
