<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Excel;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\Holliday;
use Database\Repository\Navigation;
use Database\Repository\UserAddress;
use Database\Repository\SupervisionEvent;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Repository\User as RepositoryUser;
use Database\Object\SupervisionEvent as ObjectSupervisionEvent;

class SupervisionController extends ApiController
{
    // Get functions
    protected function getFill($view, $id = null)
    {
        $repo = new SupervisionEvent;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_CALENDAR)) {
            $items = $repo->getByUserId($currentUserId);

            foreach ($items as $event) {
                $this->appendToJson(data: [
                    "id" => $event->id,
                    "start" => $event->start,
                    "end" => $event->end,
                    "backgroundColor" => $event->linked->school->color,
                    "borderColor" => $event->linked->school->color,
                    "classNames" => [
                        "text-auto"
                    ],
                ]);
            }
        }
    }

    protected function getSettings($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    // Post functions
    protected function postFill($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')?->getValue();
        $date = Helpers::input()->post('date')?->getValue();
        $start = Helpers::input()->post('start')?->getValue();
        $end = Helpers::input()->post('end')?->getValue();

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $start = $date . " " . $start;
            $end = $date . " " . $end;

            $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;

            if ($settings['block']['past']['enabled']) {
                $pastDate = Clock::now()->toDateTime();
                if ($settings['block']['past']['amount'] !== 0) $pastDate->modify("-" . $settings['block']['past']['amount']);
                $pastDate = Clock::at($pastDate->format('Y-m-d'));

                if ($settings['lastPayDate'] && $pastDate->isBeforeOrEqualTo(Clock::at($settings['lastPayDate']))) $pastDate = Clock::at($settings['lastPayDate']);

                if (Clock::at($date)->isBefore($pastDate)) $this->setToast("U kan geen middagtoezicht inboeken voor {$pastDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
            }

            if ($settings['block']['future']['enabled']) {
                $futureDate = Clock::now()->toDateTime();
                if ($settings['block']['future']['amount'] !== 0) $futureDate->modify("+" . $settings['block']['future']['amount']);
                $futureDate = Clock::at($futureDate->format('Y-m-d'));

                if (Clock::at($date)->isAfter($futureDate)) $this->setToast("U kan geen middagtoezicht inboeken na {$futureDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
            }

            if ($this->validationIsAllGood()) {
                $hollidayRepo = new Holliday;
                $isHolliday = $hollidayRepo->dateContainsHolliday($start);

                $supervisionEventRepo = new SupervisionEvent;

                $hasOverlap = $supervisionEventRepo->detectOverlap($start, $end, User::getLoggedInUser()->id, $id);
                $spansMoreThenOneDay = !Strings::equal(Clock::at($start)->format("Y-m-d"), Clock::at($end)->format("Y-m-d"));

                if ($isHolliday) {
                    $this->setValidation("start", "Starttijdstip mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                    $this->setValidation("end", "Eindtijdstip mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                }

                if ($this->validationIsAllGood()) {
                    if (!empty($hasOverlap)) $this->setToast("Je overlapt met een andere toezicht...", self::VALIDATION_STATE_INVALID);
                    else if ($spansMoreThenOneDay) $this->setToast("Een toezicht kan niet doorgaan in de nacht...", self::VALIDATION_STATE_INVALID);
                    else {
                        $existingEvent = (!is_null($id) ? $supervisionEventRepo->get($id)[0] : new ObjectSupervisionEvent);

                        $existingEvent->userId = User::getLoggedInUser()->id;
                        $existingEvent->schoolId = $schoolId;

                        if (!is_null($start)) $existingEvent->start = Clock::at($start)->format("Y-m-d H:i:s");
                        if (!is_null($end)) $existingEvent->end = Clock::at($end)->format("Y-m-d H:i:s");

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
        $_settings = Helpers::input()->all();
        $settings = [];
        foreach ($_settings as $k => $v) $settings[str_replace("_", ".", $k)] = $v;
        $settings = General::normalizeArray($settings);

        $repo = new Navigation;
        $item = Arrays::first($repo->get(Session::get("moduleSettingsId")));
        $item->settings = array_replace_recursive($item->settings, $settings);

        $repo->set($item, ['settings']);
        $this->setToast("De instellingen zijn opgeslagen!");
    }

    protected function postExport($view, $id = null)
    {
        $per = Helpers::input()->post('per')->getValue();
        $school = Helpers::input()->post('school');

        if (!is_null($school)) $school = $school->getValue();
        if (is_array($school) && !Strings::contains($school, ";")) {
            $s = [];
            foreach ($school as $sch)
                $s[] = $sch->getValue();

            $school = $s;
        } else if (Strings::contains($school, ";")) {
            $school = explode(";", $school);
        } else $school = [$school];
        $start = Helpers::input()->post('start')->getValue();
        $end = Helpers::input()->post('end')->getValue();
        $exportAs = Helpers::input()->post('exportAs')->getValue();

        if (Input::empty($school)) $this->setValidation("school", "Scholen moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($start) || Input::empty($start)) $this->setValidation("start", "Start datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($end) || Input::empty($end)) $this->setValidation("end", "Eind datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $start .= " 00:00:00";
            $end .= " 23:59:59";

            if (Strings::equal($per, "school") && Strings::equal($exportAs, 'xlsx')) $this->exportPerSchoolAsXlsx($school, $start, $end);
            else if (Strings::equal($per, "school") && Strings::equal($exportAs, 'pdf')) $this->exportPerSchoolAsPdf($school, $start, $end);
            else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "xlsx")) $this->exportPerTeacherAsXlsx($school, $start, $end);
            else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "pdf")) $this->exportPerTeacherAsPdf($school, $start, $end);
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    // Delete functions
    protected function deleteFill($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new SupervisionEvent;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het middagtoezicht op '{$item->start}' is verwijderd!");
        }

        $this->setCloseModal();
        $this->setReloadCalendar();
    }

    // Export functions
    protected function exportPerSchoolAsXlsx($schoolIds, $start, $end)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
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
        $overviewRow = $startRow;
        $overviewColumn = $startColumn;

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "School", true, border: "b");
        $overviewColumn++;

        foreach ($monthsBetweenDates as $month) {
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $month, true, border: "b");
            $overviewColumn++;
        }

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "Totaal", true, border: "bl");
        $overviewColumn++;
        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "in decimalen", true, border: "b");

        $overviewRow++;

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

            $schoolRow = $startRow;
            $schoolColumn = $startColumn;

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "Leerkracht", true, border: "b");
            $schoolColumn++;

            foreach ($monthsBetweenDates as $month) {
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $month, true, border: "b");
                $schoolColumn++;
            }

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "Totaal", true, border: "bl");
            $schoolColumn++;
            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "in decimalen", true, border: "b");

            $schoolRow++;

            foreach ($groupedEvents as $user => $events) {
                $userTotalMinutes = 0;

                $schoolColumn = $startColumn;
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $user);
                $schoolColumn++;

                foreach ($monthsBetweenDates as $month) {
                    $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($events[$month]['time'], 60) . "u " . str_pad(($events[$month]['time'] % 60), 2, "0", STR_PAD_LEFT) . "m");
                    $userTotalMinutes += $events[$month]['time'] ?? 0;
                    $schoolTotalMinutesPerMonth[$month] += $events[$month]['time'] ?? 0;
                    $schoolColumn++;
                }

                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($userTotalMinutes, 60) . "u " . str_pad(($userTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", border: "l");
                $schoolColumn++;
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($userTotalMinutes / 60, 2, ",", "."));

                $schoolTotalMinutes += $userTotalMinutes;

                $schoolRow++;
            }

            $schoolColumn = $startColumn;
            $schoolColumn++;
            foreach ($monthsBetweenDates as $month) $schoolColumn++;

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($schoolTotalMinutes, 60) . "u " . str_pad(($schoolTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", border: "t", borderStyle: Border::BORDER_DOUBLE);
            $schoolColumn++;
            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($schoolTotalMinutes / 60, 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

            $overviewColumn = $startColumn;
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $school->name, true, link: "sheet://'{$school->name}'!A1");
            $overviewColumn++;

            foreach ($monthsBetweenDates as $month) {
                $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($schoolTotalMinutesPerMonth[$month], 60) . "u" . str_pad(($schoolTotalMinutesPerMonth[$month] % 60), 2, "0", STR_PAD_LEFT) . "m");
                $overviewColumn++;
            }

            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($schoolTotalMinutes, 60) . "u " . str_pad(($schoolTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", border: "l");
            $overviewColumn++;
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format($schoolTotalMinutes / 60, 2, ",", "."));

            $overviewTotalMinutes += $schoolTotalMinutes;

            $overviewRow++;
        }

        $overviewColumn = $startColumn;
        $overviewColumn++;
        foreach ($monthsBetweenDates as $month) $overviewColumn++;

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($overviewTotalMinutes, 60) . "u " . str_pad(($overviewTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m", border: "t", borderStyle: Border::BORDER_DOUBLE);
        $overviewColumn++;
        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format($overviewTotalMinutes / 60, 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function exportPerSchoolAsPdf($schoolIds, $start, $end)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $zipFileName = "Middagtoezichten - Export Per School.zip";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        foreach ($schoolIds as $index => $schoolId) {
            $schoolTotalMinutes = 0;
            $groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end);

            $school = $schoolRepo->get($schoolId)[0];
            $pdf = new PDF($school->name, "{$folder}/{$school->name}.pdf", "L", "Middagtoezichten - Overzicht: {$school->name}");

            $pdf->AddPage();
            $pdf->Cell(60, 10, 'Startdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($start)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Einddatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($end)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Laatste uitbetalingsdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($lastPayDate)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');

            $table = [];
            $table['header'][] = [
                "title" => "Leerkracht",
                "border" => "B",
                "width" => 60
            ];

            foreach ($monthsBetweenDates as $month) {
                $table['header'][] = [
                    'title' => $month,
                    "border" => "B"
                ];
            }

            $table['header'][] = [
                "title" => "Totaal",
                "border" => "LB",
                "width" => 30
            ];

            $table['header'][] = [
                "title" => "in decimalen",
                "border" => "B",
                "width" => 30
            ];

            foreach ($groupedEvents as $user => $events) {
                $userTotalMinutes = 0;

                $table['data'][$user][] = $user;

                foreach ($monthsBetweenDates as $month) {
                    $table['data'][$user][] = intdiv($events[$month]['time'], 60) . "u " . str_pad(($events[$month]['time'] % 60), 2, "0", STR_PAD_LEFT) . "m";
                    $userTotalMinutes += $events[$month]['time'] ?? 0;
                }

                $table['data'][$user][] = [
                    "text" => intdiv($userTotalMinutes, 60) . "u " . str_pad(($userTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m",
                    "border" => "L"
                ];

                $table['data'][$user][] = number_format($userTotalMinutes / 60, 2, ",", ".");

                $schoolTotalMinutes += $userTotalMinutes;
            }

            $table['data']['total'][] = [];
            foreach ($monthsBetweenDates as $m) {
                $table['data']['total'][] = [];
            }

            $table['data']['total'][] = [
                "text" => intdiv($schoolTotalMinutes, 60) . "u " . str_pad(($schoolTotalMinutes % 60), 2, "0", STR_PAD_LEFT) . "m",
                "border" => "T"
            ];

            $table['data']['total'][] = [
                "text" => number_format($schoolTotalMinutes / 60, 2, ",", "."),
                "border" => "T"
            ];

            $pdf->Ln(10);
            $pdf->table($table);

            $pdf->save();
        }

        $zipFile = new ZIP("{$folder}/{$zipFileName}");
        $zipFile->addDir($folder);
        $zipFile->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$zipFileName}"));
    }

    protected function exportPerTeacherAsXlsx($schoolIds, $start, $end)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Middagtoezichten - Export Per Leerkracht.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
        $excel = new Excel("{$folder}/{$filename}");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

        $startRow = 9;
        $startColumn = "A";

        foreach ($groupedEvents as $username => $userEvent) {
            $userRow = $startRow;
            $userColumn = $startColumn;

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

            $excel->setCellValue($index, "{$userColumn}{$userRow}", "School", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Datum", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Start", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Einde", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Minuten", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "in decimalen", true);
            $userRow++;

            foreach ($monthsBetweenDates as $month) {
                $monthTotal = 0;

                $monthColumn = $startColumn;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}:E{$userRow}", $month, true);
                $userRow++;

                foreach ($events[$month] as $event) {
                    $eventColumn = $startColumn;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", $event->linked->school->name);
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->start)->format("d/m/Y"));
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->start)->format("H:i"));
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->end)->format("H:i"));
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", intdiv($event->diffInMinutes, 60) . "u " . str_pad(($event->diffInMinutes % 60), 2, "0", STR_PAD_LEFT) . "m");
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", number_format(($event->diffInMinutes / 60), 2, ",", "."));

                    $userRow++;

                    $monthTotal += $event->diffInMinutes;
                }

                $monthColumn = $startColumn;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", count($events[$month] ?? []) . " toezicht(ten)", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", intdiv($monthTotal, 60) . "u " . str_pad(($monthTotal % 60), 2, "0", STR_PAD_LEFT) . "m", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", number_format(($monthTotal / 60), 2, ",", "."), border: 't', borderStyle: Border::BORDER_DOUBLE);

                $userRow++;
                $userRow++;

                $userTotal += $monthTotal;
            }

            $userColumn = $startColumn;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Totaal", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", intdiv($userTotal, 60) . "u " . str_pad(($userTotal % 60), 2, "0", STR_PAD_LEFT) . "m");
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", number_format(($userTotal / 60), 2, ",", "."));
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function exportPerTeacherAsPdf($schoolIds, $start, $end)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $zipFileName = "Middagtoezichten - Export Per Leerkracht.zip";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

        foreach ($groupedEvents as $username => $userEvent) {
            $index = array_search($username, array_keys($groupedEvents));
            $user = $userEvent['user'];
            $address = $userEvent['address'];
            $events = $userEvent['events'];

            $userTotal = 0;
            $pdf = new PDF($user->formatted->fullNameReversed, "{$folder}/{$user->formatted->fullNameReversed}.pdf", "P", "Middagtoezichten - Overzicht: {$user->formatted->fullNameReversed}");

            $pdf->AddPage();
            $pdf->Cell(60, 10, 'Hoofdschool', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $user->linked->mainSchool->name, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Adres', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $address->formatted->address, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Rekeningnummer', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $user->bankAccount, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Startdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($start)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Einddatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($end)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Laatste uitbetalingsdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($lastPayDate)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');

            $table = [];
            $table['header'][] = ["title" => "School", "width" => 50];
            $table['header'][] = ["title" => "Datum", "width" => 30];
            $table['header'][] = ["title" => "Start", "width" => 20];
            $table['header'][] = ["title" => "Einde"];
            $table['header'][] = ["title" => "Minuten", "width" => 20];
            $table['header'][] = ["title" => "in decimalen", "width" => 30];

            foreach ($monthsBetweenDates as $month) {
                $monthTotal = 0;

                $table['data'][][] = [
                    "text" => $month,
                    "width" => 0
                ];

                foreach ($events[$month] as $index => $event) {
                    $table['data'][$event->start][] = $event->linked->school->name;
                    $table['data'][$event->start][] = Clock::at($event->start)->format("d/m/Y");
                    $table['data'][$event->start][] = Clock::at($event->start)->format("H:i");
                    $table['data'][$event->start][] = Clock::at($event->end)->format("H:i");
                    $table['data'][$event->start][] = intdiv($event->diffInMinutes, 60) . "u " . str_pad(($event->diffInMinutes % 60), 2, "0", STR_PAD_LEFT) . "m";
                    $table['data'][$event->start][] = number_format(($event->diffInMinutes / 60), 2, ",", ".");

                    $monthTotal += $event->diffInMinutes;
                }

                $table['data'][$month][] =
                    [
                        "text" => count($events[$month] ?? []) . " toezicht(ten)",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => "",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => "",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => "",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => intdiv($monthTotal, 60) . "u " . str_pad(($monthTotal % 60), 2, "0", STR_PAD_LEFT) . "m",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => number_format(($monthTotal / 60), 2, ",", "."),
                        "border" => 'T'
                    ];

                $table['data'][][] = "";

                $userTotal += $monthTotal;
            }

            $table2 = [];
            $table2['header'][] = ["title" => "Totaal"];
            $table2['header'][] = ["title" => ""];
            $table2['header'][] = ["title" => ""];
            $table2['header'][] = ["title" => ""];
            $table2['header'][] = ["title" => intdiv($userTotal, 60) . "u " . str_pad(($userTotal % 60), 2, "0", STR_PAD_LEFT) . "m"];
            $table2['header'][] = ["title" => number_format(($userTotal / 60), 2, ",", ".")];

            $pdf->Ln(10);
            $pdf->table($table);

            $pdf->Ln(10);
            $pdf->table($table2);

            $pdf->save();
        }

        $zipFile = new ZIP("{$folder}/{$zipFileName}");
        $zipFile->addDir($folder);
        $zipFile->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$zipFileName}"));
    }

    protected function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end)
    {
        $eventRepo = new SupervisionEvent;
        $userRepo = new RepositoryUser;
        $eventsGrouped = [];

        $events = $eventRepo->getBySchoolId($schoolId);
        $events = Arrays::filter($events, fn($e) => Clock::at($e->start)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->end)->isBeforeOrEqualTo(Clock::at($end)));

        Arrays::each($events, fn($e) => $e->user = Arrays::firstOrNull($userRepo->get($e->userId))->formatted->fullNameReversed);
        $events = Arrays::orderBy($events, "user");

        foreach ($events as $event) $eventsGrouped[$event->user][Clock::at($event->start)->format("F Y")]['time'] += $event->diffInMinutes;

        return $eventsGrouped;
    }

    protected function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds)
    {
        $eventsRepo = new SupervisionEvent;
        $userRepo = new RepositoryUser;
        $userAddressRepo = new UserAddress;
        $schoolRepo = new School;
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
            // if (is_null($address)) {
            //     unset($eventsGrouped[$user->username]);
            //     continue;
            // }
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
