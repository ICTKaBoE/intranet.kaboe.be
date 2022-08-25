<?php

use Database\Repository\Helpdesk;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Database\Repository\ToolPermission;
use Helpers\Icon;

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
            'value' => 'priorityHtml',
            'text' => 'Prioriteit',
            'width' => 100
        ],
        [
            'value' => 'schoolName',
            'text' => 'School',
            'width' => 150
        ],
        [
            'value' => 'subject',
            'text' => 'Onderwerp'
        ],
        [
            'value' => 'creatorName',
            'text' => 'Aangemaakt door',
            'width' => 200
        ],
        [
            'value' => 'assignedToName',
            'text' => 'Toegewezen aan',
            'width' => 200
        ],
        [
            'value' => 'statusHtml',
            'text' => 'Status',
            'width' => 100
        ],
        [
            'value' => 'age',
            'width' => 200,
            'align' => 'right'
        ]
    ]
];

$helpdesk = new Helpdesk;
Arrays::setNestedValue($return, ['rows'], $helpdesk->get());

echo Json::safeEncode($return);
