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
use Database\Repository\BikePrice;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserProfile;
use Database\Repository\ModuleSetting;
use Database\Repository\BikeEventHomeWork;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Repository\UserHomeWorkDistance;
use Database\Object\ModuleSetting as ObjectModuleSetting;
use Database\Object\BikeEventHomeWork as ObjectBikeEventHomeWork;
use Database\Object\UserHomeWorkDistance as ObjectUserHomeWorkDistance;

class BikeController extends ApiController
{
	public function post()
	{
		$date = Helpers::input()->post('date')->getValue();

		$bikeEventHomeWorkRepo = new BikeEventHomeWork;
		$userProfileRepo = new UserProfile;
		$userHomeWorkDistanceRepo = new UserHomeWorkDistance;
		$bikePriceRepo = new BikePrice;

		$userProfile = $userProfileRepo->getByUserId(User::getLoggedInUser()->id);
		$existingBikeEvent = $bikeEventHomeWorkRepo->getByIdAndDate(User::getLoggedInUser()->id, $date);
		$userHomeWorkDistances = Arrays::orderBy($userHomeWorkDistanceRepo->getByUserId(User::getLoggedInUser()->id), 'id');

		if (!empty($userHomeWorkDistances)) {
			if (is_null($existingBikeEvent)) {
				$userHomeWorkDistances = Arrays::first($userHomeWorkDistances);

				$existingBikeEvent = new ObjectBikeEventHomeWork([
					'userId' => User::getLoggedInUser()->id,
					'userAddressId' => $userHomeWorkDistances->startAddressId,
					'userHomeWorkDistanceId' => $userHomeWorkDistances->id,
					'userMainSchoolId' => $userProfile->mainSchoolId,
					'date' => $date,
					'endSchoolId' => $userHomeWorkDistances->endSchoolId,
					'distance' => $userHomeWorkDistances->distance,
					'pricePerKm' => $bikePriceRepo->getBetween($date)->amount
				]);
			} else {
				$nextUserHomeWorkDistance = $userHomeWorkDistances[0];

				foreach ($userHomeWorkDistances as $index => $uhwd) {
					if (Strings::equal($uhwd->id, $existingBikeEvent->userHomeWorkDistanceId)) {
						$nextUserHomeWorkDistance = $userHomeWorkDistances[$index + 1];
						break;
					}
				}

				$existingBikeEvent->userAddressId = $nextUserHomeWorkDistance->startAddressId;
				$existingBikeEvent->userMainSchoolId = $userProfile->mainSchoolId;
				$existingBikeEvent->userHomeWorkDistanceId = $nextUserHomeWorkDistance->id;
				$existingBikeEvent->distance = $nextUserHomeWorkDistance->distance;
				$existingBikeEvent->endSchoolId = $nextUserHomeWorkDistance->endSchoolId;
				$existingBikeEvent->pricePerKm = $bikePriceRepo->getBetween($date)->amount;
			}

			$bikeEventHomeWorkRepo->set($existingBikeEvent);
		}

		$bikeEventHomeWorkRepo = null;
		$userProfileRepo = null;
		$userHomeWorkDistanceRepo = null;
		$bikePriceRepo = null;

		$this->setReload();
		$this->handle();
	}

	public function postDistance($prefix, $method, $id = false)
	{
		$alias = Helpers::input()->post('alias')->getValue();
		$startAddress = Helpers::input()->post('startAddressId')->getValue();
		$endSchool = Helpers::input()->post('endSchoolId')->getValue();
		$distance = Helpers::input()->post('distance')->getValue();
		$color = Helpers::input()->post('color', false)->getValue();

		if (!Input::check($alias) || Input::empty($alias)) $this->setValidation("alias", "Alias moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($startAddress) || Input::empty($startAddress)) $this->setValidation("startAddressId", "Start adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($endSchool) || Input::empty($endSchool)) $this->setValidation("endSchoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($distance) || Input::empty($distance)) $this->setValidation("distance", "Afstand moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($color) || Input::empty($color)) $this->setValidation("color", "Kleur moet aangeduid zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			if ($id) {
				$distanceRepo = new UserHomeWorkDistance;
				$distanceObject = $distanceRepo->get($id)[0];

				$distanceObject->alias = $alias;
				$distanceObject->startAddressId = $startAddress;
				$distanceObject->endSchoolId = $endSchool;
				$distanceObject->distance = $distance;
				$distanceObject->color = $color;

				$distanceRepo->set($distanceObject);
			} else {
				$existingDistances = (new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id);

				foreach ($existingDistances as $eDistance) {
					if (Strings::equal($eDistance->alias, $alias)) {
						$this->setValidation("alias", "Er bestaat al een afstand met alias '{$alias}'!", self::VALIDATION_STATE_INVALID);
						break;
					} else if (Strings::equal($eDistance->startAddressId, $startAddress) && Strings::equal($eDistance->endSchoolId, $endSchool)) {
						$this->setValidation("endSchool", "Er bestaat al een rit met hetzelfde start adres en school!", self::VALIDATION_STATE_INVALID);
						break;
					}
				}

				if ($this->validationIsAllGood()) {
					$distanceObject = new ObjectUserHomeWorkDistance([
						'userId' => User::getLoggedInUser()->id,
						'alias' => $alias,
						'startAddressId' => (int)$startAddress,
						'endSchoolId' => (int)$endSchool,
						'distance' => (float)$distance,
						'color' => $color
					]);

					(new UserHomeWorkDistance)->set($distanceObject);
				}
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/bike/distances");
		$this->handle();
	}

	public function deleteDistance($prefix, $id)
	{
		$ids = explode("-", $id);

		$repo = new UserHomeWorkDistance;

		foreach ($ids as $id) {
			$object = $repo->get($id)[0];
			$object->deleted = 1;

			$repo->set($object);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/bike/distances");
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
		$module = (new Module)->getByModule('bike');
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
		$module = (new Module)->getByModule('bike');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
		$schoolRepo = new School();
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "Fietsvergoeding - Export Per School.zip";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

		foreach ($schoolIds as $index => $schoolId) {
			$schoolTotalDistance = $schoolTotalPrice = 0;
			$groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end);

			$school = $schoolRepo->get($schoolId)[0];
			$pdf = new PDF($school->name, "{$folder}/{$school->name}.pdf", "L", "Fietsvergoeding - Overzicht: {$school->name}");

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
				"border" => "B",
				"width" => 30
			];

			foreach ($groupedEvents as $user => $events) {
				$userTotalDistance = $userTotalPrice = 0;

				$table['data'][$user][] = $user;

				foreach ($monthsBetweenDates as $month) {
					$table['data'][$user][] = number_format(($events[$month]['distance'] ?? 0), 2, ",", ".") . " km";
					$userTotalDistance += $events[$month]['distance'] ?? 0;
					$userTotalPrice += $events[$month]['price'] ?? 0;
				}

				$table['data'][$user][] = [
					"text" => number_format($userTotalDistance, 2, ",", ".") . " km",
					"border" => "L"
				];
				$table['data'][$user][] = "€ " . number_format($userTotalPrice, 2, ",", ".");

				$schoolTotalDistance += $userTotalDistance;
				$schoolTotalPrice += $userTotalPrice;
			}

			$table['data']['total'][] = [];
			foreach ($monthsBetweenDates as $m) {
				$table['data']['total'][] = [];
			}

			$table['data']['total'][] = [
				"text" => number_format($schoolTotalDistance, 2, ",", ".") . " km",
				"border" => "T"
			];
			$table['data']['total'][] = [
				"text" => "€ " . number_format($schoolTotalPrice, 2, ",", "."),
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
		$filename = "Fietsvergoeding - Export Per School.xlsx";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

		// $overview = [];
		$startRow = 6;
		$startColumn = "A";

		$excel = new Excel("{$folder}/{$filename}");
		$excel->setSheetTitle(0, "Overzicht");
		$excel->setCellValue(0, "A1:P1", "Fietsvergoeding - Overzicht per school", true, 14);
		$excel->setCellValue(0, "A2", "Startdatum");
		$excel->setCellValue(0, "B2", Clock::at($start)->format("d/m/Y"));
		$excel->setCellValue(0, "A3", "Einddatum");
		$excel->setCellValue(0, "B3", Clock::at($end)->format("d/m/Y"));
		$excel->setCellValue(0, "A4", "Laatste uitbetalingsdatum");
		$excel->setCellValue(0, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

		$overviewTotalDistance = $overviewTotalPrice = 0;
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
		$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "", true, border: "b");

		$overviewRow++;

		foreach ($schoolIds as $index => $schoolId) {
			$schoolTotalDistance = $schoolTotalPrice = 0;
			$schoolTotalDistancePerMonth = [];
			$groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end);

			$school = $schoolRepo->get($schoolId)[0];
			$excel->createSheet($index + 1, $school->name);
			$excel->setCellValue($index + 1, "A1:P1", "Fietsvergoeding - {$school->name}", true, 14);
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
			$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "", true, border: "b");

			$schoolRow++;

			foreach ($groupedEvents as $user => $events) {
				$userTotalDistance = $userTotalPrice = 0;

				$schoolColumn = $startColumn;
				$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $user);
				$schoolColumn++;

				foreach ($monthsBetweenDates as $month) {
					$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format(($events[$month]['distance'] ?? 0), 2, ",", ".") . " km");
					$userTotalDistance += $events[$month]['distance'] ?? 0;
					$schoolTotalDistancePerMonth[$month] += $events[$month]['distance'] ?? 0;
					$userTotalPrice += $events[$month]['price'] ?? 0;
					$schoolColumn++;
				}

				$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($userTotalDistance, 2, ",", ".") . " km", border: "l");
				$schoolColumn++;
				$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "€ " . number_format($userTotalPrice, 2, ",", "."));

				$schoolTotalDistance += $userTotalDistance;
				$schoolTotalPrice += $userTotalPrice;

				$schoolRow++;
			}

			$schoolColumn = $startColumn;
			$schoolColumn++;
			foreach ($monthsBetweenDates as $month) $schoolColumn++;

			$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($schoolTotalDistance, 2, ",", ".") . " km", border: "t", borderStyle: Border::BORDER_DOUBLE);
			$schoolColumn++;
			$excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "€ " . number_format($schoolTotalPrice, 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

			$overviewColumn = $startColumn;
			$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $school->name, true, link: "sheet://'{$school->name}'!A1");
			$overviewColumn++;

			foreach ($monthsBetweenDates as $month) {
				$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($schoolTotalDistancePerMonth[$month] ?? 0), 2, ",", ".") . " km");
				$overviewColumn++;
			}

			$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($schoolTotalDistance ?? 0), 2, ",", ".") . " km", border: "l");
			$overviewColumn++;
			$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "€ " .  number_format(($schoolTotalPrice ?? 0), 2, ",", "."));

			$overviewTotalDistance += $schoolTotalDistance;
			$overviewTotalPrice += $schoolTotalPrice;

			$overviewRow++;
		}

		$overviewColumn = $startColumn;
		$overviewColumn++;
		foreach ($monthsBetweenDates as $month) $overviewColumn++;

		$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($overviewTotalDistance ?? 0), 2, ",", ".") . " km", border: "t", borderStyle: Border::BORDER_DOUBLE);
		$overviewColumn++;
		$excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "€ " .  number_format(($overviewTotalPrice ?? 0), 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

		$excel->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
	}

	private function exportPerTeacherAsPdf($schoolIds, $start, $end)
	{
		$module = (new Module)->getByModule('bike');
		$lastPayDate = (new ModuleSetting)->getByModuleAndKey($module->id, "lastPayDate")->value;
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "Fietsvergoeding - Export Per Leerkracht.zip";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
		$groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

		foreach ($groupedEvents as $username => $userEvent) {
			$index = array_search($username, array_keys($groupedEvents));
			$user = $userEvent['user'];
			$profile = $userEvent['profile'];
			$address = $userEvent['address'];
			$events = $userEvent['events'];

			$userTotalSingle = $userTotalDouble = $userTotalPrice = 0;
			$pdf = new PDF($user->fullNameReversed, "{$folder}/{$user->fullNameReversed}.pdf", "P", "Fietsvergoeding - Overzicht: {$user->fullNameReversed}");

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
			$table['header'][] = ["title" => "Afstand - Enkel"];
			$table['header'][] = ["title" => "Afstand - Dubbel"];
			$table['header'][] = ["title" => "Vergoeding/km"];
			$table['header'][] = ["title" => "Vergoeding totaal"];

			foreach ($monthsBetweenDates as $month) {
				$monthTotalSingle = $monthTotalDouble = $monthTotalPrice = 0;

				$table['data'][][] = [
					"text" => $month,
					"width" => 0
				];

				foreach ($events[$month] as $event) {
					$table['data'][$event->date][] = Clock::at($event->date)->format("d/m/Y");
					$table['data'][$event->date][] = number_format($event->distance, 2, ",", ".") . " km";
					$table['data'][$event->date][] = number_format($event->distance * 2, 2, ",", ".") . " km";
					$table['data'][$event->date][] = "€ " . number_format($event->pricePerKm, 2, ",", ".");
					$table['data'][$event->date][] = "€ " . number_format($event->pricePerKm * ($event->distance * 2), 2, ",", ".");

					$monthTotalSingle += $event->distance;
					$monthTotalDouble += $event->distance * 2;
					$monthTotalPrice += $event->pricePerKm * ($event->distance * 2);
				}

				$table['data'][$month][] =
					[
						"text" => count($events[$month] ?? []) . " rit(ten)",
						"border" => 'T'
					];

				$table['data'][$month][] =
					[
						"text" => number_format($monthTotalSingle, 2, ",", ".") . " km",
						"border" => 'T'
					];

				$table['data'][$month][] =
					[
						"text" => number_format($monthTotalDouble, 2, ",", ".") . " km",
						"border" => 'T'
					];

				$table['data'][$month][] =
					[
						"text" => "",
						"border" => 'T'
					];

				$table['data'][$month][] =
					[
						"text" => "€ " . number_format($monthTotalPrice, 2, ",", "."),
						"border" => 'T'
					];

				$table['data'][][] = "";

				$userTotalSingle += $monthTotalSingle;
				$userTotalDouble += $monthTotalDouble;
				$userTotalPrice += $monthTotalPrice;
			}

			$table2 = [];
			$table2['header'][] = ["title" => "Totaal"];
			$table2['header'][] = ["title" => number_format($userTotalSingle, 2, ",", ".") . " km"];
			$table2['header'][] = ["title" => number_format($userTotalDouble, 2, ",", ".") . " km"];
			$table2['header'][] = ["title" => ""];
			$table2['header'][] = ["title" => "€ " . number_format($userTotalPrice, 2, ",", ".")];

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
		$filename = "Fietsvergoeding - Export Per Leerkracht.xlsx";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
		$excel = new Excel("{$folder}/{$filename}");
		$groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds);

		$startRow = 9;
		$startColumn = "A";

		foreach ($groupedEvents as $username => $userEvent) {
			$userRow = $startRow;
			$userColumn = $startColumn;

			$userTotalSingle = $userTotalDouble = $userTotalPrice = 0;

			$index = array_search($username, array_keys($groupedEvents));
			$user = $userEvent['user'];
			$profile = $userEvent['profile'];
			$address = $userEvent['address'];
			$events = $userEvent['events'];

			if ($index == 0) $excel->setSheetTitle($index, $userEvent['user']->fullNameReversed);
			else $excel->createSheet($index, $userEvent['user']->fullNameReversed);

			$excel->setCellValue($index, "A1:E1", "Fietsvergoeding - Overzicht: {$user->fullNameReversed}", true, 14);
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
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Afstand - Enkel", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Afstand - Dubbel", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Vergoeding/km", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Vergoeding totaal", true);
			$userRow++;

			foreach ($monthsBetweenDates as $month) {
				$monthTotalSingle = $monthTotalDouble = $monthTotalPrice = 0;

				$monthColumn = $startColumn;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}:E{$userRow}", $month, true);
				$userRow++;

				foreach ($events[$month] as $event) {
					$eventColumn = $startColumn;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->date)->format("d/m/Y"));
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", number_format($event->distance, 2, ",", ".") . " km");
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", number_format($event->distance * 2, 2, ",", ".") . " km");
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", "€ " . number_format($event->pricePerKm, 2, ",", "."));
					$eventColumn++;
					$excel->setCellValue($index, "{$eventColumn}{$userRow}", "€ " . number_format($event->pricePerKm * ($event->distance * 2), 2, ",", "."));

					$userRow++;

					$monthTotalSingle += $event->distance;
					$monthTotalDouble += $event->distance * 2;
					$monthTotalPrice += $event->pricePerKm * ($event->distance * 2);
				}

				$monthColumn = $startColumn;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", count($events[$month] ?? []) . " rit(ten)", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", number_format($monthTotalSingle, 2, ",", ".") . " km", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", number_format($monthTotalDouble, 2, ",", ".") . " km", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
				$monthColumn++;
				$excel->setCellValue($index, "{$monthColumn}{$userRow}", "€ " . number_format($monthTotalPrice, 2, ",", "."), border: 't', borderStyle: Border::BORDER_DOUBLE);

				$userRow++;
				$userRow++;

				$userTotalSingle += $monthTotalSingle;
				$userTotalDouble += $monthTotalDouble;
				$userTotalPrice += $monthTotalPrice;
			}

			$userColumn = $startColumn;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "Totaal", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", number_format($userTotalSingle, 2, ",", ".") . " km", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", number_format($userTotalDouble, 2, ",", ".") . " km", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "", true);
			$userColumn++;
			$excel->setCellValue($index, "{$userColumn}{$userRow}", "€ " . number_format($userTotalPrice, 2, ",", "."), true);
		}

		$excel->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
	}

	private function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end)
	{
		$eventRepo = new BikeEventHomeWork;
		$localUsersRepo = new LocalUser();
		$eventsGrouped = [];

		$events = $eventRepo->getBySchoolId($schoolId);
		$events = Arrays::filter($events, fn ($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));

		Arrays::each($events, fn ($e) => $e->user = $localUsersRepo->get($e->userId)[0]->fullNameReversed);
		$events = Arrays::orderBy($events, "user");

		foreach ($events as $event) {
			if (Strings::equal($event->distance, 0)) continue;
			$eventsGrouped[$event->user][Clock::at($event->date)->format("M Y")]['distance'] += floatval($event->distance) * 2;
			$eventsGrouped[$event->user][Clock::at($event->date)->format("M Y")]['price'] += floatval($event->pricePerKm) * (floatval($event->distance) * 2);
		}

		return $eventsGrouped;
	}

	private function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds)
	{
		$eventsRepo = new BikeEventHomeWork;
		$localUsersRepo = new LocalUser;
		$userProfileRepo = new UserProfile;
		$userAddressRepo = new UserAddress;
		$schoolRepo = new School;
		$eventsGrouped = [];

		$users = Arrays::orderBy($localUsersRepo->get(), "name");

		foreach ($users as $user) {
			if (Strings::isBlank($user->username)) continue;
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

			$events = Arrays::filter($events, fn ($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));
			$events = Arrays::orderBy($events, "date");

			foreach ($events as $event) {
				if (Strings::equal($event->distance, 0)) continue;
				$eventsGrouped[$user->username]['events'][Clock::at($event->date)->format("F Y")][] = $event;
			}
		}

		return $eventsGrouped;
	}
}
