<?php

namespace Helpers;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation\TableDef;
use Database\Repository\Navigation\Navigation;

abstract class Table
{
    static private function FindTableDef()
    {
        $url = trim(Helpers::getReletiveUrl(), "/");
        $url = explode("/", $url);

        $navRepo = new Navigation;
        $navItem = $navRepo->getByParentIdAndLink($navRepo->getByLink($url[1])->id, $url[2]);

        return (new TableDef)->getByNavigationId($navItem->id);
    }

    static public function Format($tabledef = null, $checkbox = true)
    {
        if (!$tabledef) $tabledef = self::FindTableDef();

        $defaultOrder = $rows = [];
        $defaultOrder = Arrays::map(Arrays::orderBy(Arrays::filter($tabledef, fn($r) => $r->defaultOrder), "defaultOrderOrder"), fn($r) => [$checkbox ? $r->order : $r->order - 1, $r->defaultOrderDirection]);

        foreach ($tabledef as $row) {
            if ($row->render) {
                $row->render = [
                    "_" => "display",
                    "sort" => "sort"
                ];
            }

            $row->defaultContent = "";
            $rows[] = $row;
        }

        return [$defaultOrder, $rows];
    }
}
