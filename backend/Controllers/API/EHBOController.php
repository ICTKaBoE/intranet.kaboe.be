<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\User;
use Helpers\Filter;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\EHBO\EHBO;
use Database\Repository\School\School;
use Database\Repository\EHBO\FirstHelp;
use Database\Repository\EHBO\VictimType;
use Database\Repository\EHBO\Description;
use Database\Object\EHBO\EHBO as EHBOEHBO;

class EHBOController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "ehbo";

    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new EHBO;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId', 'creatorUserId']);

            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAll($view, $id = null)
    {
        $repo = new EHBO;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId']);

            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getDescription($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Description)->get();
            $items[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];
            $this->appendToJson('items', $items);
        }
    }

    protected function getFirstHelp($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new FirstHelp)->get();
            $items[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];
            $this->appendToJson('items', $items);
        }
    }

    protected function getVictimType($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new VictimType)->get();
            $items[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];
            $this->appendToJson('items', $items);
        }
    }

    protected function getSettings($view)
    {
        $this->getNavigationSettings();
    }

    // Post functions
    protected function postMine($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function postAll($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function post($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new EHBO;

        $_fields = [
            "schoolId" => ["mandatory" => true],
            "place" => ["mandatory" => true],
            "description" => ["mandatory" => true],
            "descriptionOther" => ["mandatory" => true, "preconditions" => ["description" => SELECT_OTHER_ID]],
            "firstHelpDateTime" => ["mandatory" => true],
            "firstHelp" => ["mandatory" => true],
            "firstHelpOther" => ["mandatory" => true, "preconditions" => ["firstHelp" => SELECT_OTHER_ID]],
            "victimType" => ["mandatory" => true],
            "victimId",
            "firstHelper" => ["mandatory" => true],
            "witness" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new EHBOEHBO);
            $item->fillWithPostData();
            if (!$item->creatorUserId) $item->creatorUserId = User::getLoggedInUser()->id;

            $repo->set($item);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings()
    {
        $this->postNavigationSettings();
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "school" => ["mandatory" => true],
            "start" => ["default" => null],
            "end" => ["default" => null]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (is_array($fields['school']) && !Strings::contains($fields['school'], ";")) {
            $s = [];
            foreach ($fields['school'] as $sch)
                $s[] = $sch->getValue();

            $fields['school'] = $s;
        } else if (Strings::contains($fields['school'], ";")) {
            $fields['school'] = explode(";", $fields['school']);
        } else $fields['school'] = [$fields['school']];

        if ($this->validationIsAllGood()) {
            if ($fields['start']) $fields['start'] .= " 00:00:00";
            if ($fields['end']) $fields['end'] .= " 23:59:59";

            if (Strings::equal($fields['exportAs'], 'xlsx')) $this->exportAsXlsx($fields['school'], $fields['start'], $fields['end']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Export functions
    protected function exportAsXlsx($schoolIds, $start, $end)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/ehbo/" . date("YmdHis"));
        $filename = "EHBO Register.xlsx";

        $items = $this->getAllGroupedBySchool($schoolIds, $start, $end);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($schoolIds as $index => $schoolId) {
            $school = $schoolRepo->getById($schoolId);

            if ($index == 0) $excel->setSheetTitle($index, $school->name);
            else $excel->createSheet($index, $school->name);

            $excel->setCellValue($index, "A1:P1", "EHBO Register - {$school->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", is_null(Strings::trimToNull($start)) ? "" : Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", is_null(Strings::trimToNull($end)) ? "" : Clock::at($end)->format("d/m/Y"));

            $table = [];
            $table["header"] = [
                "Aangegeven op",
                "Door",
                "Plaats",
                "Beschrijving",
                "Eerste hulp",
                "Slachtoffer",
                "Getuige"
            ];

            foreach ($items[$schoolId] as $i => $item) {
                $table["data"][$i] = [
                    $item->formatted->creationDateTime->display,
                    $item->linked->creatorUser->formatted->fullNameReversed,
                    $item->place,
                    $item->formatted->description,
                    $item->formatted->firstHelpWithDateTime,
                    $item->formatted->victim,
                    $item->witness
                ];
            }

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getAllGroupedBySchool($schoolIds, $start, $end)
    {
        $return = [];
        $repo = new EHBO;

        foreach ($schoolIds as $schoolId) {
            $return[$schoolId] = $repo->getBySchoolId($schoolId);
            if ($start) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->creationDateTime)->isAfterOrEqualTo(Clock::at($start)));
            if ($end) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->creationDateTime)->isBeforeOrEqualTo(Clock::at($end)));
        }

        return $return;
    }
}
