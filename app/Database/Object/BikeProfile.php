<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class BikeProfile extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'upn',
        'address_street',
        'address_number',
        'address_bus',
        'address_zipcode',
        'address_city',
        'address_country',
        'mainSchool',
        'distance1',
        'distance2',
        'bankAccount',
        'deleted'
    ];
}
