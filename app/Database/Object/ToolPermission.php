<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Icon;

class ToolPermission extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'toolId',
        'upn',
        'read',
        'write',
        'react',
        'export',
        'changeSettings',
        'locked',
        'deleted'
    ];

    public function init()
    {
        $this->readIcon = Icon::load($this->read ? "check" : "x");
        $this->writeIcon = Icon::load($this->write ? "check" : "x");
        $this->exportIcon = Icon::load($this->export ? "check" : "x");
        $this->changeSettingsIcon = Icon::load($this->changeSettings ? "check" : "x");
        $this->lockedIcon = Icon::load($this->locked ? "check" : "x");
    }
}
