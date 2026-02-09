<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\User;
use Helpers\Filter;
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
use Database\Repository\Absent\Substitute;
use Database\Object\Absent\Absent as AbsentAbsent;

class AbsentController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "absent";

    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new Absent;

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
        $repo = new Absent;

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

    protected function getSubstitute($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Substitute)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
        }
    }

    protected function getPayment($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Payment)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
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
        $repo = new Absent;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "absentUserId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "substituteBy" => ["mandatory" => true],
            "substituteByOther" => ["mandatory" => true, "preconditions" => ["substituteBy" => SELECT_OTHER_ID]],
            "volume" => ["mandatory" => true, "placeholder" => "__/__"],
            "start" => ["mandatory" => true],
            "end",
            "paymentOfSubstitute" => ["mandatory" => true],
            "paymentOfSubstituteOther" => ["mandatory" => true, "preconditions" => ["paymentOfSubstitute" => SELECT_OTHER_ID]],
            "absentNoteReceived" => ["mandatory" => true],
            "notes",
            "finished" => ["convert" => "bool", "default" => 0]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new AbsentAbsent);
            $item->fillWithPostData();
            if (!$item->creatorUserId) $item->creatorUserId = User::getLoggedInUser()->id;
            if ($fields['finished']) $item->finishedByUserId = User::getLoggedInUser()->id;
            else $item->finishedByUserId = NULL;

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

            $this->export($fields['school'], $fields['start'], $fields['end']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Export functions
    private function export($schoolIds, $start, $end)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/absent/" . date("YmdHis"));
        $filename = "Afwezigheid Personeel.xlsx";

        $items = $this->getAllGroupedBySchool($schoolIds, $start, $end);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($schoolIds as $index => $schoolId) {
            $school = $schoolRepo->getById($schoolId);

            if ($index == 0) $excel->setSheetTitle($index, $school->name);
            else $excel->createSheet($index, $school->name);

            $excel->setCellValue($index, "A1:P1", "Afwezigheid Personeel - {$school->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", is_null(Strings::trimToNull($start)) ? "" : Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", is_null(Strings::trimToNull($end)) ? "" : Clock::at($end)->format("d/m/Y"));

            $table = [];
            $table["header"] = [
                "Aangegeven op",
                "Door",
                "Afwezige",
                "Volume",
                "Wordt vervangen door",
                "Start",
                "Einde",
                "Betaling vervanger",
                "Ziektebriefje ontvangen",
                "Opmerking"
            ];

            foreach ($items[$schoolId] as $i => $item) {
                $table["data"][$i] = [
                    $item->formatted->creationDateTime->display,
                    $item->linked->creatorUser->formatted->fullNameReversed,
                    $item->linked->absentUser->formatted->fullNameReversed,
                    $item->volume,
                    $item->formatted->substituteBy,
                    $item->formatted->start->display,
                    $item->formatted->end->display,
                    $item->formatted->paymentOfSubstitute,
                    $item->formatted->absentNoteReceived,
                    $item->notes
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
        $repo = new Absent;

        foreach ($schoolIds as $schoolId) {
            $return[$schoolId] = $repo->getBySchoolId($schoolId);
            if ($start) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->start)->isAfterOrEqualTo(Clock::at($start)));
            if ($end) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->end)->isBeforeOrEqualTo(Clock::at($end)));
        }

        return $return;
    }
}
