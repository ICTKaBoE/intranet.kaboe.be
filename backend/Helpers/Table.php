<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;

abstract class Table
{
    static public function Format($tabledef, $checkbox = true)
    {
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
