<?php

namespace Controllers\API;

use Helpers\HTML;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Setting;
use Database\Repository\Navigation;
use Database\Repository\SecurityGroup;
use Database\Repository\GeneralMessage;
use Database\Repository\SecurityGroupUser;
use Database\Object\SecurityGroup as ObjectSecurityGroup;
use Database\Object\SecurityGroupUser as ObjectSecurityGroupUser;

class NavigationController extends ApiController
{
    // Get functions
    protected function getList($view, $id = null)
    {
        $repo = new Navigation;
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->getByParentId(0);
            $items = Arrays::map($items, fn($i) => $i->toArray(true));
            $items = array_merge([["id" => SELECT_ALL_ID, "name" => SELECT_ALL_VALUE]], $items);
            $this->appendToJson('items', $items);
        }
    }
}
