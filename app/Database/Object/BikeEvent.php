<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class BikeEvent extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'upn',
        'date',
        'distance',
        'distanceInKm',
        'deleted'
    ];
}
