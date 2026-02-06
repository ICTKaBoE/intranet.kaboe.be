<?php

namespace Controllers\API;

use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School\Course;
use Database\Repository\School\School;
use Database\Repository\School\Department;
use Database\Repository\School\Hour;

class SchoolController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "school";

    // Get Functions
    protected function getList($view, $id)
    {
        $repo = new School;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $_items = $repo->get(filters: $filters);

            $optgroups = array_values(Arrays::map(Arrays::filter($_items, fn($i) => $i->virtual), fn($i) => ["id" => $i->id, "name" => $i->name]));
            $items = array_values(Arrays::filter($_items, fn($i) => !$i->virtual));
            Arrays::each($items, fn($i) => $i->optgroup = $i->parentSchoolId);

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get();
            $this->appendToJson('raw', General::processTemplate(Arrays::map($items, fn($i) => $i->toArray(true))));
        }
    }

    protected function getAll($view, $id = null)
    {
        $repo = new School;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getDepartment($view, $id = null)
    {
        $repo = new Department;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $optgroups = Arrays::map(Arrays::uniqueBy($items, fn($i) => $i->schoolId), fn($i) => $i->linked->school);

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getCourse($view, $id = null)
    {
        $repo = new Course;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $optgroups = null;

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getHours($view, $id = null)
    {
        $repo = new Hour;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $optgroups = Arrays::map(Arrays::uniqueBy($items, fn($i) => $i->schoolId), fn($i) => $i->linked->school);

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    // Post Functions

}
