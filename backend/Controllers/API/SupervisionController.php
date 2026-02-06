<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Form;
use Helpers\Excel;
use Security\User;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\Export\Export as ExportExport;
use Database\Repository\Holliday;
use Database\Repository\User\Address;
use Database\Repository\Export\Export;
use Database\Repository\School\School;
use Database\Repository\SupervisionEvent;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Repository\Navigation\Setting;
use Database\Repository\User\User as RepositoryUser;
use Database\Object\SupervisionEvent as ObjectSupervisionEvent;

class SupervisionController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "supervision";

    // Get functions
    protected function getFill($view, $id = null)
    {
        $repo = new SupervisionEvent;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_CALENDAR)) {
            $items = $repo->getByUserId($currentUserId);

            // foreach ($items as $event) {
            Arrays::each(
                $items,
                fn($i) =>
                $this->appendToJson(data: [
                    "id" => $i->id,
                    "start" => $i->start,
                    "end" => $i->end,
                    "backgroundColor" => $i->linked->school->color,
                    "borderColor" => $i->linked->school->color,
                    "classNames" => [
                        "text-auto"
                    ],
                ])
            );
        }
    }

    protected function getSettings($view, $id = null)
    {
        return $this->getNavigationSettings();
    }

    // Post functions
    protected function postFill($view, $id = null)
    {
        $settingsRepo = new Setting;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "date",
            "start",
            "end"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $fields['start'] = $fields['date'] . " " . $fields['start'];
            $fields['end'] = $fields['date'] . " " . $fields['end'];

            if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.enabled")->value, "bool")) {
                $pastDate = Clock::now()->toDateTime();
                if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.amount")->value !== 0) $pastDate->modify("-" . $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.amount")->value);
                $pastDate = Clock::at($pastDate->format('Y-m-d'));

                if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value && $pastDate->isBeforeOrEqualTo(Clock::at($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value))) $pastDate = Clock::at($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value);

                if (Clock::at($fields['date'])->isBefore($pastDate)) $this->setToast("U kan geen middagtoezicht inboeken voor {$pastDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
            }

            if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.enabled")->value, "bool")) {
                $futureDate = Clock::now()->toDateTime();
                if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.amount")->value !== 0) $futureDate->modify("+" . $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.amount")->value);
                $futureDate = Clock::at($futureDate->format('Y-m-d'));

                if (Clock::at($fields['date'])->isAfter($futureDate)) $this->setToast("U kan geen middagtoezicht inboeken na {$futureDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
            }

            if ($this->validationIsAllGood()) {
                $hollidayRepo = new Holliday;
                $isHolliday = $hollidayRepo->dateContainsHolliday($fields['start']);

                $supervisionEventRepo = new SupervisionEvent;

                $hasOverlap = $supervisionEventRepo->detectOverlap($fields['start'], $fields['end'], User::getLoggedInUser()->id, $id);
                $spansMoreThenOneDay = !Strings::equal(Clock::at($fields['start'])->format("Y-m-d"), Clock::at($fields['end'])->format("Y-m-d"));

                if ($isHolliday) {
                    $this->setValidation("start", "Starttijdstip mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                    $this->setValidation("end", "Eindtijdstip mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                }

                if ($this->validationIsAllGood()) {
                    if (!empty($hasOverlap)) $this->setToast("Je overlapt met een andere toezicht...", self::VALIDATION_STATE_INVALID);
                    else if ($spansMoreThenOneDay) $this->setToast("Een toezicht kan niet doorgaan in de nacht...", self::VALIDATION_STATE_INVALID);
                    else {
                        $existingEvent = $supervisionEventRepo->getById($id) ?? new ObjectSupervisionEvent;
                        $existingEvent->fillWithPostData();
                        $existingEvent->userId = User::getLoggedInUser()->id;
                        if (!is_null($fields['start'])) $existingEvent->start = Clock::at($fields['start'])->format("Y-m-d H:i:s");
                        if (!is_null($fields['end'])) $existingEvent->end = Clock::at($fields['end'])->format("Y-m-d H:i:s");

                        $supervisionEventRepo->set($existingEvent);

                        $this->setToast("Middagtoezicht op " . Clock::at($existingEvent->start)->format("d/m/Y") . " van " . Clock::at($existingEvent->start)->format("H:i") . " tot en met " . Clock::at($existingEvent->end)->format("H:i") . " geregistreerd!");
                    }
                }
            }
        }

        $this->setCloseModal();
        $this->setReloadCalendar();
    }

    protected function postSettings($view, $id = null)
    {
        $this->postNavigationSettings();
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "per",
            "school" => ["mandatory" => true],
            "start" => ["mandatory" => true],
            "end" => ["mandatory" => true],
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
            $fields['start'] .= " 00:00:00";
            $fields['end'] .= " 23:59:59";

            if (Strings::equal($fields['per'], "school")) $this->exportPerSchool($fields['school'], $fields['start'], $fields['end'], $fields['type']);
            else if (Strings::equal($fields['per'], "teacher")) $this->exportPerTeacher($fields['school'], $fields['start'], $fields['end'], $fields['type']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);

        $this->handle();
    }

    // Delete functions
    protected function deleteFill($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new SupervisionEvent;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het middagtoezicht op '{$item->start}' is verwijderd!");
        }

        $this->setCloseModal();
        $this->setReloadCalendar();
    }

    // Export functions
    private function exportPerSchool($schoolIds, $start, $end)
    {
        $lastPayDate = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value;

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/supervision/" . date("YmdHis"));
        $filename = "Middagtoezichten - Export Per School.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        // $overview = [];
        $startRow = 6;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");
        $excel->setSheetTitle(0, "Overzicht");
        $excel->setCellValue(0, "A1:P1", "Middagtoezichten - Overzicht per school", true, 14);
        $excel->setCellValue(0, "A2", "Startdatum");
        $excel->setCellValue(0, "B2", Clock::at($start)->format("d/m/Y"));
        $excel->setCellValue(0, "A3", "Einddatum");
        $excel->setCellValue(0, "B3", Clock::at($end)->format("d/m/Y"));
        $excel->setCellValue(0, "A4", "Laatste uitbetalingsdatum");
        $excel->setCellValue(0, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

        $overviewTotalMinutes = 0;

        $overviewTable = [];
        $overviewTable["header"] = ["School"];

        foreach ($monthsBetweenDates as $month) $overviewTable["header"][] = $month;

        $overviewTable["header"][] = ["text" => "Totaal", "border" => "bl"];
        $overviewTable["header"][] = "in decimalen";

        foreach ($schoolIds as $index => $schoolId) {
            $schoolTotalMinutes = 0;
            $schoolTotalMinutesPerMonth = [];
            $groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end);

            $school = $schoolRepo->get($schoolId)[0];
            $excel->createSheet($index + 1, $school->name);
            $excel->setCellValue($index + 1, "A1:P1", "Middagtoezichten - {$school->name}", true, 14);
            $excel->setCellValue($index + 1, "A2", "Startdatum");
            $excel->setCellValue($index + 1, "B2", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A3", "Einddatum");
            $excel->setCellValue($index + 1, "B3", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A4", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index + 1, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

            $schoolTable = [];
            $schoolTable["header"] = ["Leerkracht"];

            foreach ($monthsBetweenDates as $month) $schoolTable["header"][] = $month;

            $schoolTable["header"][] = ["text" => "Totaal", "border" => "bl"];
            $schoolTable["header"][] = "in decimalen";

            $schoolTableRow = 0;
            foreach ($groupedEvents as $user => $events) {
                $userTotalMinutes = 0;

                $schoolTable["data"][$schoolTableRow][] = $user;

                foreach ($monthsBetweenDates as $month) {
                    $schoolTable["data"][$schoolTableRow][] = intdiv($events[$month]['time'], 60) . "u " . str_pad(($events[$month]['time'] % 60), 2, "0", STR_PAD_LEFT) . "m";
                    $userTotalMinutes += $events[$month]['time'] ?? 0;
                    $schoolTotalMinutesPerMonth[$month] += $events[$month]['time'] ?? 0;
                }

                $schoolTable["data"][$schoolTableRow][] = ["text" => intdiv($userTotalMinutes, 60) . "u " . str_pad(($userTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", "border" => "l"];
                $schoolTable["data"][$schoolTableRow][] = number_format($userTotalMinutes / 60, 2, ",", ".");

                $schoolTotalMinutes += $userTotalMinutes;
                $schoolTableRow++;
            }

            $schoolTable["data"][$schoolTableRow][] = "";
            foreach ($monthsBetweenDates as $month) $schoolTable["data"][$schoolTableRow][] = "";
            $schoolTable["data"][$schoolTableRow][] = ["text" => intdiv($schoolTotalMinutes, 60) . "u " . str_pad(($schoolTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];
            $schoolTable["data"][$schoolTableRow][] = ["text" => number_format($schoolTotalMinutes / 60, 2, ",", "."), "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];

            $overviewTable["data"][$index][] = ["text" => $school->name, "link" => "sheet://'{$school->name}'!A1"];
            foreach ($monthsBetweenDates as $month) $overviewTable["data"][$index][] = intdiv($schoolTotalMinutesPerMonth[$month], 60) . "u" . str_pad(($schoolTotalMinutesPerMonth[$month] % 60), 2, "0", STR_PAD_LEFT) . "m";
            $overviewTable["data"][$index][] = ["text" => intdiv($schoolTotalMinutes, 60) . "u " . str_pad(($schoolTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", "border" => "l"];
            $overviewTable["data"][$index][] = number_format($schoolTotalMinutes / 60, 2, ",", ".");

            $excel->table($index + 1, $startColumn, $startRow, $schoolTable);

            $overviewTotalMinutes += $schoolTotalMinutes;
        }

        $overviewTable["data"][count($schoolIds)][] = "";
        foreach ($monthsBetweenDates as $month) $overviewTable["data"][count($schoolIds)][] = "";
        $overviewTable["data"][count($schoolIds)][] = ["text" => intdiv($overviewTotalMinutes, 60) . "u " . str_pad(($overviewTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", "border" => "tl", "borderStyle" => Border::BORDER_DOUBLE];
        $overviewTable["data"][count($schoolIds)][] = ["text" => number_format($overviewTotalMinutes / 60, 2, ",", "."), "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];

        $excel->table(0, $startColumn, $startRow, $overviewTable);
        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    private function exportPerTeacher($schoolIds, $start, $end)
    {
        $lastPayDate = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value;

        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/supervision/" . date("YmdHis"));
        $filename = "Middagtoezichten - Export Per Leerkracht.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
        $excel = new Excel("{$folder}/{$filename}");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

        $startRow = 9;
        $startColumn = "A";

        foreach ($groupedEvents as $username => $userEvent) {
            $row = 0;
            $table = [];

            $userTotal = 0;

            $index = array_search($username, array_keys($groupedEvents));
            $user = $userEvent['user'];
            $address = $userEvent['address'];
            $events = $userEvent['events'];

            if ($index == 0) $excel->setSheetTitle($index, $userEvent['user']->formatted->fullNameReversed);
            else $excel->createSheet($index, $userEvent['user']->formatted->fullNameReversed);

            $excel->setCellValue($index, "A1:E1", "Middagtoezichten - Overzicht: {$user->formatted->fullNameReversed}", true, 14);
            $excel->setCellValue($index, "A2", "Hoofdschool");
            $excel->setCellValue($index, "B2:E2", $user->linked->mainSchool->name);
            $excel->setCellValue($index, "A3", "Adres");
            $excel->setCellValue($index, "B3:E3", $address->formatted->address);
            $excel->setCellValue($index, "A4", "Rekeningnummer");
            $excel->setCellValue($index, "B4:E4", $user->bankAccount);
            $excel->setCellValue($index, "A5", "Startdatum");
            $excel->setCellValue($index, "B5:E5", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A6", "Einddatum");
            $excel->setCellValue($index, "B6:E6", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index, "A7", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index, "B7:E7", Clock::at($lastPayDate)->format("d/m/Y"));

            $table["header"] = [
                "School",
                "Datum",
                "Start",
                "Einde",
                "Minuten",
                "in decimalen"
            ];

            foreach ($monthsBetweenDates as $month) {
                $monthTotal = 0;

                $table["data"][$row][] = ["text" => $month, "bold" => true];
                $row++;

                foreach ($events[$month] as $event) {
                    $table["data"][$row] = [
                        $event->linked->school->name,
                        Clock::at($event->start)->format("d/m/Y"),
                        Clock::at($event->start)->format("H:i"),
                        Clock::at($event->end)->format("H:i"),
                        intdiv($event->diffInMinutes, 60) . "u " . str_pad(($event->diffInMinutes % 60), 2, "0", STR_PAD_LEFT) . "m",
                        number_format(($event->diffInMinutes / 60), 2, ",", ".")
                    ];

                    $monthTotal += $event->diffInMinutes;
                    $row++;
                }

                $table["data"][$row] = [
                    [
                        "text" => count($events[$month] ?? []) . " toezicht(ten)",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => "",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => "",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => "",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => intdiv($monthTotal, 60) . "u " . str_pad(($monthTotal % 60), 2, "0", STR_PAD_LEFT) . "m",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => number_format(($monthTotal / 60), 2, ",", "."),
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ]
                ];

                $row++;
                $table["data"][$row] = [];

                $userTotal += $monthTotal;
                $row++;
            }

            $table["data"][$row] = [
                [
                    "text" => "Totaal",
                    "bold" => true
                ],
                "",
                "",
                "",
                intdiv($userTotal, 60) . "u " . str_pad(($userTotal % 60), 2, "0", STR_PAD_LEFT) . "m",
                number_format(($userTotal / 60), 2, ",", ".")
            ];

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end)
    {
        $eventRepo = new SupervisionEvent;
        $userRepo = new RepositoryUser;
        $eventsGrouped = [];

        $events = $eventRepo->getBySchoolId($schoolId);
        $events = Arrays::filter($events, fn($e) => Clock::at($e->start)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->end)->isBeforeOrEqualTo(Clock::at($end)));

        Arrays::each($events, fn($e) => $e->user = $userRepo->getById($e->userId)->formatted->fullNameReversed);
        $events = Arrays::orderBy($events, "user");

        foreach ($events as $event) $eventsGrouped[$event->user][Clock::at($event->start)->format("F Y")]['time'] += $event->diffInMinutes;

        return $eventsGrouped;
    }

    protected function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds)
    {
        $eventsRepo = new SupervisionEvent;
        $userRepo = new RepositoryUser;
        $userAddressRepo = new Address;
        $eventsGrouped = [];
        $users = Arrays::orderBy($userRepo->get(), "name");

        foreach ($users as $user) {
            if (Strings::isBlank($user->username) || is_null($user->id) || is_null($user)) continue;
            $eventsGrouped[$user->username]['user'] = $user;

            if (!Arrays::contains($allowedSchoolIds, $user->mainSchoolId)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $address = $userAddressRepo->getCurrentByUserId($user->id);
            $eventsGrouped[$user->username]['address'] = $address;

            $events = $eventsRepo->getByUserId($user->id);
            if (!count($events)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $events = Arrays::filter($events, fn($e) => Clock::at($e->start)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->end)->isBeforeOrEqualTo(Clock::at($end)));
            $events = Arrays::orderBy($events, "start");

            foreach ($events as $event) $eventsGrouped[$user->username]['events'][Clock::at($event->start)->format("F Y")][] = $event;
        }

        return $eventsGrouped;
    }
}
