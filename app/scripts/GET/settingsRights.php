<?php

use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Database\Repository\ToolPermission;
use Helpers\Icon;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'header' => [
        [
            'type' => 'check',
            'value' => 'id',
        ],
        [
            'value' => 'upn',
            'text' => 'E-mail'
        ],
        [
            'type' => 'icon',
            'icon' => 'readIcon',
            'text' => Icon::load("eye"),
        ],
        [
            'type' => 'icon',
            'icon' => 'writeIcon',
            'text' => Icon::load("pencil"),
        ],
        [
            'type' => 'icon',
            'icon' => 'exportIcon',
            'text' => Icon::load("file-export"),
        ],
        [
            'type' => 'icon',
            'icon' => 'changeSettingsIcon',
            'text' => Icon::load("adjustments"),
        ],
        [
            'type' => 'icon',
            'icon' => 'lockedIcon',
            'text' => Icon::load("lock"),
        ]
    ]
];

$toolId = $_GET['id'] ?? 0;

$toolPermissions = new ToolPermission;
Arrays::setNestedValue($return, ['rows'], $toolId > 0 ? Arrays::orderBy($toolPermissions->getByToolId($toolId), 'upn') : []);

echo Json::safeEncode($return);
