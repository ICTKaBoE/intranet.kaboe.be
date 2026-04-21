<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\GUID;
use Security\User;
use Helpers\Filter;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\IWE\IWE;
use Database\Object\IWE\IWE as IWEIWE;
use Database\Repository\School\School;

class IWEController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "iwe";

    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new IWE;

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
        $repo = new IWE;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
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
        $repo = new IWE;

        $_fields = [
            "schoolId" => ["mandatory" => true],
            "buildingId" => ["mandatory" => true],
            "roomId" => ["mandatory" => true],
            "name" => ["mandatory" => true],
            "amount" => ["mandatory" => true],
            "brand" => ["mandatory" => true],
            "model" => ["mandatory" => true],
            "ownedBySchool" => ["type" => Input::INPUT_TYPE_BOOL],
            "schoolTakesOwnership" => ["type" => Input::INPUT_TYPE_BOOL],
            "manual" => ["type" => "file"],
            "ce" => ["type" => "file"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new IWEIWE);
            $item->fillWithPostData();
            if (!$item->creatorUserId) $item->creatorUserId = User::getLoggedInUser()->id;
            $item->guid = $item->guid ?? GUID::create();
            $repo->set($item);

            $file = $fields["manual"];
            if ($file && $file[0]->getSize() > 0) {
                $ext = $file[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/iwe/{$item->guid}");
                $file[0]->move(LOCATION_FILES . "/iwe/{$item->guid}/manual.{$ext}");
            }

            $file = $fields["ce"];
            if ($file && $file[0]->getSize() > 0) {
                $ext = $file[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/iwe/{$item->guid}");
                $file[0]->move(LOCATION_FILES . "/iwe/{$item->guid}/ce.{$ext}");
            }

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "school" => ["mandatory" => true]
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
            $this->export($fields['school']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Export functions
    private function export($schoolIds)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/iwe/" . date("YmdHis"));
        $filename = "Inventaris Arbeidsmiddelen.xlsx";

        $items = $this->getAllGroupedBySchool($schoolIds);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($schoolIds as $index => $schoolId) {
            $school = $schoolRepo->getById($schoolId);

            if ($index == 0) $excel->setSheetTitle($index, $school->name);
            else $excel->createSheet($index, $school->name);

            $excel->setCellValue($index, "A1:P1", "Inventaris Arbeidsmiddelen - {$school->name}", true, 14);

            $table = [];
            $table["header"] = [
                "Aangegeven op",
                "Door",
                "Plaats",
                "Aantal",
                "Naam",
                "Merk",
                "Model",
                "School is eigenaar",
                "Overname door school",
                "Handleiding",
                "CE-kenteken"
            ];

            foreach ($items[$schoolId] as $i => $item) {
                $table["data"][$i] = [
                    $item->formatted->creationDateTime->display,
                    $item->linked->creatorUser->formatted->fullNameReversed,
                    $item->formatted->location,
                    $item->amount,
                    $item->name,
                    $item->brand,
                    $item->model,
                    $item->formatted->ownedBySchool,
                    $item->formatted->schoolTakesOwnership,
                    $item->formatted->manualLink,
                    $item->formatted->ceLink
                ];
            }

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getAllGroupedBySchool($schoolIds)
    {
        $return = [];
        $repo = new IWE;

        foreach ($schoolIds as $schoolId) {
            $return[$schoolId] = $repo->getBySchoolId($schoolId);
        }

        return $return;
    }
}
