<?php

use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Database\Repository\ToolPermission;
use Helpers\Icon;
use O365\Objects\User;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [
    'template' => "<label class='form-selectgroup-item flex-fill'><input type='checkbox' name='' value='\${mail}' class='form-selectgroup-input'><div class='form-selectgroup-label d-flex align-items-center p-3'><div class='me-3'><span class='form-selectgroup-check'></span></div><div class='form-selectgroup-label-content d-flex align-items-center'><div><div class='font-weight-medium'>\${displayName}</div></div></div></div></label>",
    'headerTemplate' => "<div class='form-selectgroup-item flex-fill'><div class='form-selectgroup-label d-flex align-items-center p-3'><div class='form-selectgroup-label-content d-flex align-items-center'><div><div class='font-weight-medium'>\${header}</div></div></div></div></div>",
    'headers' => range("A", "Z")
];

echo Json::safeEncode($return);
