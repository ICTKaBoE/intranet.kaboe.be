<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Excel;
use Security\User;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserProfile;
use Database\Repository\ModuleSetting;
use Database\Repository\BikeEventHomeWork;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Object\ModuleSetting as ObjectModuleSetting;
use Database\Object\SupervisionEvent as ObjectSupervisionEvent;
use Database\Repository\SupervisionEvent;

class SupervisionController extends ApiController
{
	public function post()
	{
		$id = Helpers::input()->post('id')?->getValue();
		$start = Helpers::input()->post('start')?->getValue();
		$end = Helpers::input()->post('end')?->getValue();
		$delete = Helpers::input()->post('delete')?->getValue();

		$supervisionEventRepo = new SupervisionEvent;

		$hasOverlap = $supervisionEventRepo->detectOverlap($start, $end, $id);
		$spansMoreThenOneDay = !Strings::equal(Clock::at($start)->format("Y-m-d"), Clock::at($end)->format("Y-m-d"));

		if (!empty($hasOverlap)) {
			$this->appendToJson("action", "revert");
			$this->setToast("Je overlapt met een andere toezicht...", self::VALIDATION_STATE_INVALID);
		} else if ($spansMoreThenOneDay) {
			$this->appendToJson("action", "revert");
			$this->setToast("Een toezicht kan niet doorgaan in de nacht...", self::VALIDATION_STATE_INVALID);
		} else {
			$userProfileRepo = new UserProfile;
			$userProfile = $userProfileRepo->getByUserId(User::getLoggedInUser()->id);
			$existingEvent = (!is_null($id) ? $supervisionEventRepo->get($id)[0] : new ObjectSupervisionEvent);

			$existingEvent->userId = User::getLoggedInUser()->id;
			$existingEvent->userMainSchoolId = $userProfile->mainSchoolId;

			if (!is_null($start)) $existingEvent->start = Clock::at($start)->format("Y-m-d H:i:s");
			if (!is_null($end)) $existingEvent->end = Clock::at($end)->format("Y-m-d H:i:s");
			if (!is_null($delete)) $existingEvent->deleted = true;

			$supervisionEventRepo->set($existingEvent);

			$this->setReload();
		}

		$this->handle();
	}

	public function settings()
	{
		$settings = [
			"lastPayDate" => "1970-01-01",
			"blockPast" => "false",
			"blockFuture" => "false",
			"blockPastAmount" => 0,
			"blockPastType" => "d",
			"blockFutureAmount" => 0,
			"blockFutureType" => "d",
			"blockPastOnLastPayDate" => "true"
		];

		$module = (new Module)->getByModule('supervision');
		$moduleSettingRepo = new ModuleSetting;

		foreach ($settings as $setting => $defaultValue) {
			$moduleSetting = $moduleSettingRepo->getByModuleAndKey($module->id, $setting);
			$value = isset($_POST[$setting]) ? Helpers::input()->post($setting)->getValue() : $defaultValue;

			if (is_null($moduleSetting)) {
				$moduleSetting = new ObjectModuleSetting([
					'moduleId' => $module->id,
					'key' => $setting,
					'value' => Input::convertToBool($value)
				]);
			} else {
				$moduleSetting->value = Input::convertToBool($value);
			}

			$moduleSettingRepo->set($moduleSetting);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		}
		$this->handle();
	}

	public function export()
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

			if (Strings::equal($per, "school") && Strings::equal($exportAs, 'pdf')) $this->exportPerSchoolAsPdf($school, $start, $end);
			else if (Strings::equal($per, "school") && Strings::equal($exportAs, 'xlsx')) $this->exportPerSchoolAsXlsx($school, $start, $end);
			else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "pdf")) $this->exportPerTeacherAsPdf($school, $start, $end);
			else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "xlsx")) $this->exportPerTeacherAsXlsx($school, $start, $end);
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}

	private function exportPerSchoolAsPdf($schoolIds, $start, $end)
	{
		$module = (new Module)->getByModule('supervision');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
		$schoolRepo = new School();
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "Middagtoezichten - Export Per School.zip";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

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
				"width" => 60
			];

			foreach ($groupedEvents as $user => $events) {
				$userTotalMinutes = 0;

				$table['data'][$user][] = $user;

				foreach ($monthsBetweenDates as $month) {
					$table['data'][$user][] = intdiv($events[$month]['time'], 60) . " uur " . ($events[$month]['time'] % 60) . " minuten";
					$userTotalMinutes += $events[$month]['time'] ?? 0;
				}

				$table['data'][$user][] = [
					"text" => intdiv($userTotalMinutes, 60) . " uur " . ($userTotalMinutes % 60) . " minuten",
					"border" => "L"
				];

				$schoolTotalMinutes += $userTotalMinutes;
			}

			$table['data']['total'][] = [];
			foreach ($monthsBetweenDates as $m) {
				$table['data']['total'][] = [];
			}

			$table['data']['total'][] = [
				"text" => intdiv($schoolTotalMinutes, 60) . " uur " . ($schoolTotalMinutes % 60) . " minuten",
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

	private function exportPerSchoolAsXlsx($schoolIds, $start, $end)
	{
		$module = (new Module)->getByModule('bike');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
		$schoolRepo = new School();
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$filename = "Middagtoezichten - Export Per School.xlsx";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

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

			$schoolRow++;

			foreach ($groupedEvents as $user => $events) {
				$userTotalMinutes = 0;

				$schoolColumn = $startColumn;
				$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $user);
				$schoolColumn++;

				foreach ($monthsBetweenDates as $month) {
					$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($events[$month]['time'], 60) . " uur " . ($events[$month]['time'] % 60) . " minuten");
					$userTotalMinutes += $events[$month]['time'] ?? 0;
					$schoolTotalMinutesPerMonth[$month] += $events[$month]['time'] ?? 0;
					$schoolColumn++;
				}

				$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($userTotalMinutes, 60) . " uur " . ($userTotalMinutes % 60) . " minuten", border: "l");

				$schoolTotalMinutes += $userTotalMinutes;

				$schoolRow++;
			}

			$schoolColumn = $startColumn;
			$schoolColumn++;
			foreach ($monthsBetweenDates as $month) $schoolColumn++;

			$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", intdiv($schoolTotalMinutes, 60) . " uur " . ($schoolTotalMinutes % 60) . " minuten", border: "t", borderStyle: Border::BORDER_DOUBLE);

			$overviewColumn = $startColumn;
			$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $school->name, true, link: "sheet://'{$school->name}'!A1");
			$overviewColumn++;

			foreach ($monthsBetweenDates as $month) {
				$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($schoolTotalDistancePerMonth[$month] ?? 0, 60) . " uur " . ($schoolTotalDistancePerMonth[$month] ?? 0 % 60) . " minuten");
				$overviewColumn++;
			}

			$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($schoolTotalMinutes, 60) . " uur " . ($schoolTotalMinutes % 60) . " minuten", border: "l");

			$overviewTotalMinutes += $schoolTotalMinutes;

			$overviewRow++;
		}

		$overviewColumn = $startColumn;
		$overviewColumn++;
		foreach ($monthsBetweenDates as $month) $overviewColumn++;

		$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", intdiv($overviewTotalMinutes, 60) . " uur " . ($overviewTotalMinutes % 60) . " minuten", border: "t", borderStyle: Border::BORDER_DOUBLE);

		$excel->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
	}

	private function exportPerTeacherAsPdf($schoolIds, $start, $end)
	{
		$module = (new Module)->getByModule('supervision');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "Middagtoezichten - Export Per Leerkracht.zip";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
		$groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

		foreach ($groupedEvents as $username => $userEvent) {
			$index = array_search($username, array_keys($groupedEvents));
			$user = $userEvent['user'];
			$profile = $userEvent['profile'];
			$address = $userEvent['address'];
			$events = $userEvent['events'];

			$userTotal = 0;
			$pdf = new PDF($user->fullNameReversed, "{$folder}/{$user->fullNameReversed}.pdf", "P", "Middagtoezichten - Overzicht: {$user->fullNameReversed}");

			$pdf->AddPage();
			$pdf->Cell(60, 10, 'Hoofdschool', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, $profile->mainSchool->name, ln: 1, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(60, 10, 'Adres', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, $address->formatted, ln: 1, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(60, 10, 'Rekeningnummer', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, $profile->bankAccount, ln: 1, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(60, 10, 'Startdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, Clock::at($start)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(60, 10, 'Einddatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, Clock::at($end)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(60, 10, 'Laatste uitbetalingsdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
			$pdf->Cell(0, 10, Clock::at($lastPayDate)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');

			$table = [];
			$table['header'][] = ["title" => "Datum"];
			$table['header'][] = ["title" => "Start"];
			$table['header'][] = ["title" => "Einde"];
			$table['header'][] = ["title" => "Minuten"];

			foreach ($monthsBetweenDates as $month) {
				$monthTotal = 0;

				$table['data'][][] = [
					"text" => $month,
					"width" => 0
				];

				foreach ($events[$month] as $index => $event) {
					$table['data'][$index + 1][] = Clock::at($event->start)->format("d/m/Y");
					$table['data'][$index + 1][] = Clock::at($event->start)->format("H:i");
					$table['data'][$index + 1][] = Clock::at($event->end)->format("H:i");
					$table['data'][$index + 1][] = intdiv($event->diffInMinutes, 60) . " uur " . ($event->diffInMinutes % 60) . " minuten";

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
						"text" => intdiv($monthTotal, 60) . " uur " . ($monthTotal % 60) . " minuten",
						"border" => 'T'
					];

				$table['data'][][] = "";

				$userTotal += $monthTotal;
			}

			$table2 = [];
			$table2['header'][] = ["title" => "Totaal"];
			$table2['header'][] = ["title" => ""];
			$table2['header'][] = ["title" => ""];
			$table2['header'][] = ["title" => intdiv($userTotal, 60) . " uur " . ($userTotal % 60) . " minuten"];

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

	private function exportPerTeacherAsXlsx($schoolIds, $start, $end)
	{
		$module = (new Module)->getByModule('bike');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
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
			$profile = $userEvent['profile'];
			$address = $userEvent['address'];
			$events = $userEvent['events'];

			if ($index == 0) $excel->setSheetTitle($index, $userEvent['user']->fullNameReversed);
			else $excel->createSheet($index, $userEvent['user']->fullNameReversed);

			$excel->setCellValue($index, "A1:E1", "Middagtoezichten - Overzicht: {$user->fullNameReversed}", true, 14);
			$excel->setCellValue($index, "A2", "Hoofdschool");
			$excel->setCellValue($index, "B2:E2", $profile->mainSchool->name);
			$excel->setCellValue($index, "A3", "Adres");
			$excel->setCellValue($index, "B3:E3", $address->formatted);
			$excel->setCellValue($index, "A4", "Rekeningnummer");
			$excel->setCellValue($index, "B4:E4", $profile->bankAccount);
			$excel->setCellValue($index, "A5", "Startdatum");
			$excel->setCellValue($index, "B5:E5", Clock::at($start)->format("d/m/Y"));
			$excel->setCellValue($index, "A6", "Einddatum");
			$excel->setCellValue($index, "B6:E6", Clock::at($end)->format("d/m/Y"));
			$excel->setCellValue($index, "A7", "Laatste uitbetalingsdatum");
			$excel->setCellValue($index, "B7:E7", Clock::at($lastPayDate)->format("d/m/Y"));

			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Datum", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Start", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Einde", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Minuten", true);
			$userRow++;

			foreach ($monthsBetweenDates as $month) {
				$monthTotal = 0;

				$monthColumn = $startColumn;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}:E{$userRow}", $month, true);
				$userRow++;

				foreach ($events[$month] as $event) {
					$eventColumn = $startColumn;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->start)->format("d/m/Y"));
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->start)->format("H:i"));
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->end)->format("H:i"));
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", intdiv($event->diffInMinutes, 60) . " uur " . ($event->diffInMinutes % 60) . " minuten");

					$userRow++;

					$monthTotal += $event->diffInMinutes;
				}

				$monthColumn = $startColumn;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", count($events[$month] ?? []) . " rit(ten)", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", intdiv($monthTotal, 60) . " uur " . ($monthTotal % 60) . " minuten", border: 't', borderStyle: Border::BORDER_DOUBLE);

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
			$excel->setCellValue($index, "{$userColumn}{$userRow}", intdiv($userTotal, 60) . " uur " . ($userTotal % 60) . " minuten");
		}

		$excel->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
	}

	private function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end)
	{
		$eventRepo = new SupervisionEvent;
		$localUsersRepo = new LocalUser;
		$eventsGrouped = [];

		$events = $eventRepo->getBySchoolId($schoolId);
		$events = Arrays::filter($events, fn ($e) => Clock::at($e->start)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->end)->isBeforeOrEqualTo(Clock::at($end)));

		Arrays::each($events, fn ($e) => $e->user = $localUsersRepo->get($e->userId)[0]->fullNameReversed);
		$events = Arrays::orderBy($events, "user");

		foreach ($events as $event) $eventsGrouped[$event->user][Clock::at($event->start)->format("M Y")]['time'] += $event->diffInMinutes;

		return $eventsGrouped;
	}

	private function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds)
	{
		$eventsRepo = new SupervisionEvent;
		$localUsersRepo = new LocalUser;
		$userProfileRepo = new UserProfile;
		$userAddressRepo = new UserAddress;
		$schoolRepo = new School;
		$eventsGrouped = [];
		$users = Arrays::orderBy($localUsersRepo->get(), "name");

		foreach ($users as $user) {
			if (Strings::isBlank($user->username) || is_null($user->id) || is_null($user)) continue;
			$eventsGrouped[$user->username]['user'] = $user;

			$profile = $userProfileRepo->getByUserId($user->id);
			if (is_null($profile) || !Arrays::contains($allowedSchoolIds, $profile->mainSchoolId)) {
				unset($eventsGrouped[$user->username]);
				continue;
			}

			$profile->mainSchool = $schoolRepo->get($profile->mainSchoolId)[0];
			$eventsGrouped[$user->username]['profile'] = $profile;

			$address = $userAddressRepo->getCurrentByUserId($user->id);
			if (is_null($address)) {
				unset($eventsGrouped[$user->username]);
				continue;
			}
			$eventsGrouped[$user->username]['address'] = $address;

			$events = $eventsRepo->getByUserId($user->id);
			if (!count($events)) {
				unset($eventsGrouped[$user->username]);
				continue;
			}

			$events = Arrays::filter($events, fn ($e) => Clock::at($e->start)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->end)->isBeforeOrEqualTo(Clock::at($end)));
			$events = Arrays::orderBy($events, "start");

			foreach ($events as $event) $eventsGrouped[$user->username]['events'][Clock::at($event->start)->format("F Y")][] = $event;
		}

		return $eventsGrouped;
	}
}
