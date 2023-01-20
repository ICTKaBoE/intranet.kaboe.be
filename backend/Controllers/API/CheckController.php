<?php

namespace Controllers\API;

use Helpers\ZIP;
use Helpers\Excel;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Controllers\ApiController;
use Database\Repository\School;
use Informat\Repository\Student;
use Informat\Repository\Relation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Database\Repository\SchoolInstitute;
use Database\Repository\CheckStudentRelationInsz;
use Database\Object\CheckStudentRelationInsz as ObjectCheckStudentRelationInsz;
use Ouzo\Utilities\Strings;

class CheckController extends ApiController
{
	public function studentRelation()
	{
		set_time_limit(0);

		$linkToFile = FileSystem::getLatestFile(LOCATION_BACKEND . "/externalfiles/CheckStudentRelation");
		$reader = IOFactory::createReader("Xlsx");
		$reader->setReadDataOnly(TRUE);
		$spreadsheet = $reader->load($linkToFile);
		$spreadsheet->setActiveSheetIndexByName("Form1");
		$worksheet = $spreadsheet->getActiveSheet();
		$checkStudentRelationRepo = new CheckStudentRelationInsz;
		$informatStudentRepo = new Student;
		$schoolRepo = new School;
		$schoolInstituteRepo = new SchoolInstitute;

		$data = [];
		$headers = [];

		foreach ($worksheet->getRowIterator() as $rindex => $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE);

			if ($row->getRowIndex() - 1 == 0) {
				foreach ($cellIterator as $index => $cell) {
					Arrays::setNestedValue($headers, [$index], $cell->getValue());
				}
			} else {
				foreach ($cellIterator as $index => $cell) {
					$data[$row->getRowIndex()][$headers[$index]] = $cell->getValue();
				}
			}
		}

		$data = array_values($data);

		foreach ($data as $index => $row) {
			$checkStudentRelation = $checkStudentRelationRepo->getByCheckField($row['Rijksregister kind']) ?? new ObjectCheckStudentRelationInsz;
			if (!is_null($checkStudentRelation->id)) continue;

			$school = Arrays::getValue($row, "Op welke school zit uw kind?", $checkStudentRelation->school);
			$schoolId = $schoolRepo->getByName($school)->id;
			$institutes = $schoolInstituteRepo->getBySchoolId($schoolId);

			$informatStudent = null;

			foreach ($institutes as $institute) {
				$informatStudentRepo->setInstituteNumber($institute->instituteNumber);
				$is = $informatStudentRepo->getByRRN(Arrays::getValue($row, "Rijksregister kind"));

				if (!is_null($is)) {
					$informatStudent = $is;
					break;
				}
			}

			$checkStudentRelation->informatStudentId = is_null($informatStudent) ? "" : $informatStudent->p_persoon;
			$checkStudentRelation->informatInstituteNumber = is_null($informatStudent) ? "" : $informatStudent->instelnr;

			$checkStudentRelation->checkField = Arrays::getValue($row, "Rijksregister kind", $checkStudentRelation->checkField);
			$checkStudentRelation->school = Arrays::getValue($row, "Op welke school zit uw kind?", $checkStudentRelation->school);
			$checkStudentRelation->class = Arrays::getValue($row, "In welke klas zit uw kind?", Arrays::getValue($row, "In welke klas zit uw kind?2", Arrays::getValue($row, "In welke klas zit uw kind?3", Arrays::getValue($row, "In welke klas zit uw kind?4", $checkStudentRelation->class))));
			$checkStudentRelation->childName = Arrays::getValue($row, "Naam kind", $checkStudentRelation->childName);
			$checkStudentRelation->childInsz = Arrays::getValue($row, "Rijksregister kind", $checkStudentRelation->childInsz);
			$checkStudentRelation->motherName = Arrays::getValue($row, "Naam moeder", $checkStudentRelation->motherName);
			$checkStudentRelation->motherInsz = Arrays::getValue($row, "Rijksregister moeder", $checkStudentRelation->motherInsz);
			$checkStudentRelation->fatherName = Arrays::getValue($row, "Naam vader", $checkStudentRelation->fatherName);
			$checkStudentRelation->fatherInsz = Arrays::getValue($row, "Rijksregister vader", $checkStudentRelation->fatherInsz);
			$checkStudentRelation->insertDateTime = Clock::nowAsString("Y-m-d H:i:s");

			$checkStudentRelationRepo->set($checkStudentRelation);
		}

		$this->handle();
	}

	public function postCheckStudentRelationInsz($prefix, $method, $id)
	{
		$informatStudentRepo = new Student;
		$schoolRepo = new School;
		$schoolInstituteRepo = new SchoolInstitute;

		$childInsz = Helpers::input()->post("childInsz")->getValue();
		$motherInsz = Helpers::input()->post("motherInsz")->getValue();
		$fatherInsz = Helpers::input()->post("fatherInsz")->getValue();

		if (!Input::check($childInsz, Input::INPUT_TYPE_INSZ))
			$this->setValidation('childInsz', 'Er zit een fout in het nummer!', self::VALIDATION_STATE_INVALID);

		if (!Input::check($motherInsz, Input::INPUT_TYPE_INSZ))
			$this->setValidation('motherInsz', 'Er zit een fout in het nummer!', self::VALIDATION_STATE_INVALID);

		if (!Input::check($fatherInsz, Input::INPUT_TYPE_INSZ))
			$this->setValidation('fatherInsz', 'Er zit een fout in het nummer!', self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$repo = new CheckStudentRelationInsz;
			$checkStudentRelation = $repo->get($id)[0];

			$school = $checkStudentRelation->school;
			$schoolId = $schoolRepo->getByName($school)->id;
			$institutes = $schoolInstituteRepo->getBySchoolId($schoolId);

			$informatStudent = null;

			foreach ($institutes as $institute) {
				$informatStudentRepo->setInstituteNumber($institute->instituteNumber);
				$is = $informatStudentRepo->getByRRN($childInsz);

				if (!is_null($is)) {
					$informatStudent = $is;
					break;
				}
			}

			$checkStudentRelation->informatStudentId = is_null($informatStudent) ? "" : $informatStudent->p_persoon;
			$checkStudentRelation->informatInstituteNumber = is_null($informatStudent) ? "" : $informatStudent->instelnr;

			$checkStudentRelation->childInsz = $childInsz;
			$checkStudentRelation->motherInsz = $motherInsz;
			$checkStudentRelation->fatherInsz = $fatherInsz;

			$repo->set($checkStudentRelation);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/checklists/checkStudentRelationInsz");
		$this->handle();
	}

	public function approveCheckStudentRelationInsz($prefix, $id)
	{
		$ids = explode("-", $id);
		$repo = new CheckStudentRelationInsz;

		foreach ($ids as $_id) {
			$item = $repo->get($_id)[0];
			$item->check();
			if (!$item->childInszIsCorrect || !$item->motherInszIsCorrect || !$item->fatherInszIsCorrect || !$item->foundInInformat) continue;

			$item->locked = true;

			$repo->set($item);
		}

		$this->setReload();
		$this->handle();
	}

	public function prepareForInformat($prefix, $id, $school, $class)
	{
		set_time_limit(0);

		$ids = explode("-", $id);
		$ids = Arrays::map($ids, fn ($i) => (int)$i);
		$repo = new CheckStudentRelationInsz;
		$iRepo = new SchoolInstitute;
		$sRepo = new School;
		$informatSRepo = new Student;
		$informatRRepo = new Relation;

		$school = $sRepo->getByName($school);
		$schoolInstitutes = $iRepo->getBySchoolId($school->id);

		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "importForInformat.zip";

		$checksPerInstitute = [];

		foreach ($schoolInstitutes as $institute) {
			$idsInInstitute = $repo->getByInstitute($institute->instituteNumber);

			if (Strings::isNotBlank($class) && !Strings::equal($class, SELECT_ALL_VALUES)) {
				$idsInInstitute = Arrays::filter($idsInInstitute, fn ($i) => Strings::equal($i->class, $class));
			}

			$idsInInstitute = Arrays::map($idsInInstitute, fn ($i) => $i->id);
			$idsInInstitute = Arrays::filter($ids, fn ($id) => Arrays::contains($idsInInstitute, $id));

			$checksPerInstitute[$institute->id] = $idsInInstitute;
		}

		foreach ($checksPerInstitute as $instituteId => $checks) {
			$institute = $iRepo->get($instituteId)[0];
			$informatSRepo->setInstituteNumber($institute->instituteNumber);
			$informatRRepo->setInstituteNumber($institute->instituteNumber);
			$excelName = "{$institute->instituteNumber} - {$school->name}.xlsx";

			$rowStart = 2;

			$excel = new Excel("{$folder}/{$excelName}");
			$excel->setCellValue(0, "A1", "Naam");
			$excel->setCellValue(0, "B1", "Voornaam");
			$excel->setCellValue(0, "C1", "Geboortedatum");
			$excel->setCellValue(0, "D1", "Rijksregisternummer");
			$excel->setCellValue(0, "E1", "Geslacht");
			$excel->setCellValue(0, "F1", "Begindatum");
			$excel->setCellValue(0, "G1", "Type Lpv1");
			$excel->setCellValue(0, "H1", "Naam Lpv1");
			$excel->setCellValue(0, "I1", "Voornaam Lpv1");
			$excel->setCellValue(0, "J1", "Insz Lpv1");
			$excel->setCellValue(0, "K1", "Type Lpv2");
			$excel->setCellValue(0, "L1", "Naam Lpv2");
			$excel->setCellValue(0, "M1", "Voornaam Lpv2");
			$excel->setCellValue(0, "N1", "Insz Lpv2");

			foreach ($checks as $checkId) {
				$item = $repo->get($checkId)[0];
				$item->check();
				if (!$item->locked || !$item->childInszIsCorrect || !$item->motherInszIsCorrect || !$item->fatherInszIsCorrect || !$item->foundInInformat) continue;

				$student = $informatSRepo->get($item->informatStudentId)[0];
				$relations = $informatRRepo->getByStudentId($student->p_persoon);
				$lpv1 = Arrays::firstOrNull(Arrays::filter($relations, fn ($r) => Strings::equal($r->Lpv, 1)));
				$lpv2 = Arrays::firstOrNull(Arrays::filter($relations, fn ($r) => Strings::equal($r->Lpv, 2)));

				$excel->setCellValue(0, "A{$rowStart}", $student->Naam);
				$excel->setCellValue(0, "B{$rowStart}", $student->Voornaam);
				$excel->setCellValue(0, "C{$rowStart}", $student->geboortedatum);
				$excel->setCellValue(0, "D{$rowStart}", $student->rijksregnr);
				$excel->setCellValue(0, "E{$rowStart}", $student->geslacht);
				$excel->setCellValue(0, "F{$rowStart}", $student->begindatum);

				if (is_null($lpv1) && is_null($lpv2)) {
					$excel->setCellValue(0, "G{$rowStart}", "Moeder");
					$excel->setCellValue(0, "H{$rowStart}", "");
					$excel->setCellValue(0, "I{$rowStart}", "");
					$excel->setCellValue(0, "J{$rowStart}", $item->motherInsz);
					$excel->setCellValue(0, "K{$rowStart}", "Vader");
					$excel->setCellValue(0, "L{$rowStart}", "");
					$excel->setCellValue(0, "M{$rowStart}", "");
					$excel->setCellValue(0, "N{$rowStart}", $item->fatherInsz);
				} else {
					if (!is_null($lpv1)) {
						$excel->setCellValue(0, "G{$rowStart}", $lpv1->Type);
						$excel->setCellValue(0, "H{$rowStart}", $lpv1->Naam);
						$excel->setCellValue(0, "I{$rowStart}", $lpv1->Voornaam);
						$excel->setCellValue(0, "J{$rowStart}", (Strings::equalsIgnoreCase($lpv1->Type, 'moeder') ? $item->motherInsz : $item->fatherInsz) ?? $lpv1->Insz);
					}

					if (!is_null($lpv2)) {
						$excel->setCellValue(0, "K{$rowStart}", $lpv2->Type);
						$excel->setCellValue(0, "L{$rowStart}", $lpv2->Naam);
						$excel->setCellValue(0, "M{$rowStart}", $lpv2->Voornaam);
						$excel->setCellValue(0, "N{$rowStart}", (Strings::equalsIgnoreCase($lpv2->Type, 'moeder') ? $item->motherInsz : $item->fatherInsz) ?? $lpv2->Insz);
					}
				}

				$item->published = true;
				$repo->set($item);

				$rowStart++;
			}

			$excel->save();
		}

		$zipFile = new ZIP("{$folder}/{$zipFileName}");
		$zipFile->addDir($folder);
		$zipFile->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$zipFileName}"));
		$this->setReload();
		$this->handle();
	}
}
