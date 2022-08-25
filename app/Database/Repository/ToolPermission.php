<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class ToolPermission extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_tool_permission", \Database\Object\ToolPermission::class);
    }

    public function hasPermission($toolId, $upn)
    {
        $items = $this->get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->upn, $upn));
        $items = array_values($items);

        if ($items[0]->toolId === 0 && $items[0]->read === 1) return true;

        $items = Arrays::filter($items, fn ($i) => $i->toolId == $toolId);
        $items = array_values($items);
        if (count($items) !== 0 && $items[0]->read === 1) return true;

        return false;
    }

    public function getByToolId($toolId)
    {
        $items = $this->get(order: false);
        $items = Arrays::filter($items, fn ($i) => $i->toolId == 0 || $i->toolId == $toolId);

        return array_values($items);
    }

    public function getByUpn($upn)
    {
        $items = $this->get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->upn, $upn));
        if (count($items) !== 0) return array_values($items);

        return false;
    }

    public function getByToolIdAndUpn($toolId, $upn)
    {
        $items = $this->get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->upn, $upn) && ($i->toolId == $toolId || $i->toolId == 0));
        if (count($items) == 1) return Arrays::first($items);
        else if (count($items) > 1) return array_values($items);

        return false;
    }

    public function set($object)
    {
        $alreadyPermissions = $this->getByToolIdAndUpn($object->toolId, $object->upn);

        if (!$alreadyPermissions) return $this->insert($object->toArray());
        else return $this->update($alreadyPermissions->toArray());
    }
}
