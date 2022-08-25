<?php

use Core\Config;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'setup' => [
        'clickToEdit' => [
            'value' => 'id'
        ]
    ],
    'header' => [
        [
            'type' => 'check',
            'value' => 'id',
        ],
        [
            'value' => 'id',
            'text' => 'ID',
            'width' => 50
        ],
        [
            'value' => 'name',
            'text' => 'School'
        ]
    ]
];

$_schools = Config::get("schools");
$schools = [];

foreach ($_schools as $idx => $school) $schools[] = ['id' => $idx, 'name' => $school];

Arrays::setNestedValue($return, ['rows'], $schools);

echo Json::safeEncode($return);
