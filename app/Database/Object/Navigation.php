<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Navigation extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'toolId',
        'routePage',
        'name',
        'icon',
        'order',
        'show',
        'minimumRights',
        'deleted'
    ];
}
