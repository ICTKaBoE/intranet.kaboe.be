<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\User;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Absent\Note;
use Database\Repository\Absent\Absent;
use Database\Repository\School\School;
use Database\Repository\Absent\Payment;
use Database\Repository\Accident\Status;
use Database\Repository\Absent\Substitute;
use Database\Repository\Navigation\TableDef;
use Database\Repository\Navigation\Navigation;
use Database\Object\Absent\Absent as AbsentAbsent;

class AbsentController extends ApiController
{
    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new Absent;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
                'creatorUserId' => Arrays::filter(explode(";", Helpers::url()->getParam("creatorUserId")), fn($i) => Strings::isNotBlank($i)),
            ];

            $navRepo = new Navigation;
            $navItem = $navRepo->getByParentIdAndLink($navRepo->getByParentIdAndLink(0, "absent")->id, "mine");

            [$defaultOrder, $columns] = Table::Format((new TableDef)->getByNavigationId($navItem->id), false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAll($view, $id = null)
    {
        $repo = new Absent;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            ];

            $navRepo = new Navigation;
            $navItem = $navRepo->getByParentIdAndLink($navRepo->getByParentIdAndLink(0, "absent")->id, "all");

            [$defaultOrder, $columns] = Table::Format((new TableDef)->getByNavigationId($navItem->id));
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getSubstitute($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Substitute)->get());
        }
    }

    protected function getPayment($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Payment)->get());
        }
    }

    protected function getNote($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Note)->get());
        }
    }

    protected function getSettings($view)
    {
        $this->getNavigationSettings("accident");
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
        $repo = new Absent;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "absentUserId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "substituteBy" => ["mandatory" => true],
            "substituteByOther" => ["mandatory" => true, "preconditions" => ["substituteBy" => "O"]],
            "volume" => ["mandatory" => true, "placeholder" => "__/__"],
            "start" => ["mandatory" => true],
            "end",
            "paymentOfSubstitute" => ["mandatory" => true],
            "paymentOfSubstituteOther" => ["mandatory" => true, "preconditions" => ["paymentOfSubstitute" => "O"]],
            "absentNoteReceived" => ["mandatory" => true],
            "notes"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new AbsentAbsent);
            $item->fillWithPostData();
            $item->creatorUserId = User::getLoggedInUser()->id;

            $repo->set($item);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings()
    {
        $this->postNavigationSettings("accident");
    }

    protected function postExport($view, $id = null)
    {
        $_fields = [
            "school" => ["mandatory" => true],
            "start" => ["default" => null],
            "end" => ["default" => null],
            "exportAs"
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
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Afwezigheid Personeel.xlsx";

        $items = $this->getAllGroupedBySchool($schoolIds, $start, $end);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($schoolIds as $index => $schoolId) {
            $school = $schoolRepo->getById($schoolId);

            $schoolRow = $startRow;
            $schoolColumn = $startColumn;

            if ($index == 0) $excel->setSheetTitle($index, $school->name);
            else $excel->createSheet($index, $school->name);

            $excel->setCellValue($index, "A1:P1", "Afwezigheid Personeel - {$school->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", is_null(Strings::trimToNull($start)) ? "" : Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", is_null(Strings::trimToNull($end)) ? "" : Clock::at($end)->format("d/m/Y"));

            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Aangegeven op", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Door", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Afwezige", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Volume", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Wordt vervangen door", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Start", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Einde", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Betaling vervanger", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Ziektebriefje ontvangen", true);
            $schoolColumn++;
            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Opmerking", true);
            $schoolRow++;

            foreach ($items[$schoolId] as $item) {
                $itemColumn = $startColumn;

                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->creationDateTime->display);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->linked->creatorUser->formatted->fullNameReversed);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->linked->absentUser->formatted->fullNameReversed);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->volume);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->substituteBy);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->start->display);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->end->display);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->paymentOfSubstitute);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->formatted->absentNoteReceived);
                $itemColumn++;
                $excel->setCellValue($index, "{$itemColumn}{$schoolRow}", $item->notes);
                $schoolRow++;
            }
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getAllGroupedBySchool($schoolIds, $start, $end)
    {
        $return = [];
        $repo = new Absent;

        foreach ($schoolIds as $schoolId) {
            $return[$schoolId] = $repo->getBySchoolId($schoolId);
            if ($start) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->start)->isAfterOrEqualTo(Clock::at($start)));
            if ($end) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->end)->isBeforeOrEqualTo(Clock::at($end)));
        }

        return $return;
    }
}
