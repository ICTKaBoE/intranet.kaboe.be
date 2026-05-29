<?php

namespace Helpers;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

abstract class Filter
{
    static public function Find($filters = [])
    {
        $newFilters = [];
        foreach ($filters as $filter) $newFilters[$filter] = Arrays::filter(explode(";", Helpers::url()->getParam($filter)), fn($i) => Strings::isNotBlank($i));

        return $newFilters;
    }
}
