<?php

namespace Controllers\API;

use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Holliday;

class HollidayController extends ApiController
{
    public function get($view, $what = null)
    {
        if (Strings::equal($what, null)) $this->getHolliday($view);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    private function getHolliday($view)
    {
        $repo = new Holliday;
        $items = $repo->get();

        if (Strings::equal($view, "calendar")) {
            foreach ($items as $item) {
                $this->appendToJson(data: [
                    "id" => $item->id,
                    "start" => $item->start,
                    "end" => $item->end,
                    "title" => ($item->linked->school ? $item->linked->school->name . ": " : "") . $item->name,
                    "allDay" => $item->fullDay
                ]);
            }
        }
    }
}
