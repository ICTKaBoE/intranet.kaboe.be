<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Navigation extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_navigation", \Database\Object\Navigation::class, guidField: false);
    }

    public function getByRouteGroupIdParentIdAndLink($routeGroupId, $parentId, $link)
    {
        $statement = $this->prepareSelect(filters: [
            'parentId' => $parentId,
            'link' => $link
        ]);

        $items = $this->executeSelect($statement);
        return Arrays::filter($items, fn($i) => Arrays::contains(explode(",", $i->routeGroupId), $routeGroupId));
    }

    public function getByRouteGroupIdAndParentId($routeGroupId, $parentId)
    {
        $statement = $this->prepareSelect(filters: ['parentId' => $parentId]);
        $items = $this->executeSelect($statement);
        return Arrays::filter($items, fn($i) => Arrays::contains(explode(",", $i->routeGroupId), $routeGroupId));
    }

    public function getByParentId($parentId)
    {
        $statement = $this->prepareSelect(filters: ['parentId' => $parentId]);
        return $this->executeSelect($statement);
    }

    public function getByLink($link)
    {
        $statement = $this->prepareSelect(filters: ['link' => $link]);
        return $this->executeSelect($statement);
    }

    public function getByParentIdAndLink($parentId, $link)
    {
        $statement = $this->prepareSelect(filters: [
            'parentId' => $parentId,
            'link' => $link
        ]);

        return $this->executeSelect($statement);
    }

    public function getByRouteGroupIdParentIdAndFolderId($routeGroupId, $parentId, $folderId)
    {
        $statement = $this->prepareSelect(filters: ['folderId' => $folderId, 'parentId' => $parentId]);
        $items = $this->executeSelect($statement);
        return Arrays::filter($items, fn($i) => Arrays::contains(explode(",", $i->routeGroupId), $routeGroupId));
    }
}
