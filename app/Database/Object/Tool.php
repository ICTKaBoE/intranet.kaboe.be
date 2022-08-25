<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Tool extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'routeTool',
        'icon',
        'iconColor',
        'name',
        'showInSettings',
        'order',
        'deleted'
    ];
}
