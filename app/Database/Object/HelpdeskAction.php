<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;
use Helpers\Icon;

class HelpdeskAction extends CustomObject
{
    protected $objectAttributes = [
        "id",
        "helpdeskId",
        'type',
        'info',
        "timestamp",
        "deleted"
    ];

    public function init()
    {
        $this->typeIcon = ($this->type == 'CREATE' ? 'plus' : ($this->type == 'UPDATE' ? 'pencil' : 'trash'));
        $this->typeIconHtml = Icon::load($this->typeIcon);

        $age = Clock::at($this->timestamp)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0 && $age->i == 0) $this->age = $age->s . " seconden geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0) $this->age = $age->i . " minuten geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0) $this->age = $age->h . " uren geleden";
        else if ($age->y == 0 && $age->m == 0) $this->age = $age->d . " dagen geleden";
        else if ($age->y == 0) $this->age = $age->m . " maanden geleden";
        else $this->age = $age->y . " jaren geleden";

        $this->timestampFormatted = Clock::at($this->timestamp)->format("d/m/Y H:i:s");
    }
}
