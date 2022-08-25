<?php

use Core\Config;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'header' => [
        [
            'type' => 'check',
            'value' => 'id',
        ],
        [
            'value' => 'start',
            'text' => 'Geldig van',
            'width' => 100
        ],
        [
            'value' => 'end',
            'text' => 'Geldig tot',
            'width' => 100
        ],
        [
            'value' => 'amount',
            'text' => 'Bedrag per KM'
        ]
    ]
];

Arrays::setNestedValue($return, ['rows'], Arrays::orderBy(Config::get("tool/bike/price"), 'start'));

echo Json::safeEncode($return);
