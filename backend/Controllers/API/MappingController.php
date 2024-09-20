<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Mapping;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class MappingController extends ApiController
{
    public function get($view, $what)
    {
        if (Strings::equal($what, "bikeDistanceType")) $this->getBikeDistanceType($view);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    private function getBikeDistanceType($view)
    {
        $prefix = "bike/distance/type/";
        $types = (new Mapping)->getWhereKeyStartsWith($prefix);

        if (Strings::equal($view, "select")) {
            Arrays::each($types, function ($t) use ($prefix) {
                $t->key = Strings::removePrefix($t->key, $prefix);
                $t->key = substr($t->key, strpos($t->key, "/"));
            });

            $this->appendToJson('items', $types);
        }
    }
}
