<?php

namespace Controllers\Cron;

use Database\Object\Informat\ClassGroup;
use Database\Object\Informat\ClassgroupTeacher as InformatClassgroupTeacher;
use Database\Object\Informat\Employee as InformatEmployee;
use Database\Object\Informat\EmployeeAddress;
use Database\Object\Informat\EmployeeEmail;
use Database\Object\Informat\EmployeeNumber;
use Database\Object\Informat\EmployeeOwnfield;
use Database\Object\Informat\Registration as InformatRegistration;
use Database\Object\Informat\RegistrationClass;
use Database\Object\Informat\RegistrationClassTeacher as InformatRegistrationClassTeacher;
use Database\Object\Informat\Student as InformatStudent;
use Database\Object\Informat\StudentAddress;
use Database\Object\Informat\StudentBank;
use Database\Object\Informat\StudentEmail;
use Database\Object\Informat\StudentNumber;
use Database\Object\Informat\StudentRelation;
use Database\Repository\General\Country;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup as InformatClassGroup;
use Database\Repository\Informat\ClassGroupTeacher;
use Database\Repository\Informat\Employee as RepositoryInformatEmployee;
use Database\Repository\Informat\EmployeeAddress as InformatEmployeeAddress;
use Database\Repository\Informat\EmployeeEmail as InformatEmployeeEmail;
use Database\Repository\Informat\EmployeeNumber as InformatEmployeeNumber;
use Database\Repository\Informat\EmployeeOwnfield as InformatEmployeeOwnfield;
use Database\Repository\Informat\Registration as RepositoryInformatRegistration;
use Database\Repository\Informat\RegistrationClass as InformatRegistrationClass;
use Database\Repository\Informat\RegistrationClassTeacher;
use Database\Repository\Informat\Student as RepositoryInformatStudent;
use Database\Repository\Informat\StudentAddress as InformatStudentAddress;
use Database\Repository\Informat\StudentBank as InformatStudentBank;
use Database\Repository\Informat\StudentEmail as InformatStudentEmail;
use Database\Repository\Informat\StudentNumber as InformatStudentNumber;
use Database\Repository\Informat\StudentRelation as InformatStudentRelation;
use Database\Repository\School\Institute;
use Database\Repository\School\School;
use Helpers\General;
use Helpers\Log;
use Informat\Repository\Employee;
use Informat\Repository\EmployeeOwnfield as RepositoryEmployeeOwnfield;
use Informat\Repository\EmployeePhoto;
use Informat\Repository\Registration;
use Informat\Repository\Student;
use Informat\Repository\StudentPhoto;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\FileSystem;

abstract class Informat
{
    static public function Import(...$args)
    {
        $institutes = [];
        foreach ((new School)->getImport() as $school) {
            foreach ((new Institute)->getBySchoolId($school->id) as $inst) $institutes[] = $inst;
        }
        define("_INSTITUTES_", $institutes);

        if (Arrays::keyExists($args, 'image')) {
            $studentPhoto = self::StudentPhotos();
            $employeePhoto = self::EmployeePhotos();

            return ($studentPhoto && $employeePhoto);
        } else {
            $schoolyear = _CURRENT_SCHOOLYEAR_;
            $nextSchoolyear = Arrays::keyExists($args, 'nextSchoolyear');

            if ($nextSchoolyear) {
                $schoolyear = General::getSchoolyear(Clock::now()->plusYears(1)->format("Y-m-d"));
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Switching to schoolyear {$schoolyear}");
            }

            $schoolyear = (new Schoolyear)->getByName($schoolyear);
            $student = self::Students($schoolyear);
            $registration = self::Registrations($schoolyear);
            $employee = $nextSchoolyear ? true : self::Employees($schoolyear);
            $employeeOwnfield = $nextSchoolyear ? true : self::EmployeeOwnfields($schoolyear);

            return ($student && $registration && $employee && $employeeOwnfield);
        }
    }

    // Main Functions
    static private function Students($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import students...");
        $_error_ = false;
        $informatRepo = new Student;
        $repo = new RepositoryInformatStudent;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Removing institute ID from students...");
        // foreach ($repo->get() as $student) {
        //     $student->instituteId = 0;
        //     $repo->set($student);
        // }

        foreach (_INSTITUTES_ as $institute) {
            $import = $institute->linked->school->import || $institute->linked->school->linked->parentSchool->import;

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat} - {$institute->linked->school->name} (import: " .  (!$import ? "NO" : "YES") . ")");
            if (!$import) continue;

            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Student: {$iItem->pPersoon} ({$iItem->persoonId}) - {$iItem->naam} {$iItem->voornaam}");

                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->persoonId) ?? new InformatStudent;
                    $item->informatId = Strings::trimToNull($iItem->pPersoon);
                    $item->informatGuid = Strings::trimToNull($iItem->persoonId);
                    $item->name = Strings::trimToNull($iItem->naam);
                    $item->firstName = Strings::trimToNull($iItem->voornaam);
                    $item->sex = (Strings::equalsIgnoreCase($iItem->geslacht, "m") ? "M" : "F");
                    $item->birthDate = $iItem->geboortedatum;
                    $item->birthPlace = Strings::trimToNull($iItem->geboorteplaats);
                    $item->insz = Strings::trimToNull($iItem->rijksregisternr ?: $iItem->bisnr ?: null);
                    $item->instituteId = $institute->id;

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->adressen as $adres) self::CreateStudentAddress($item->id, $adres, true);
                    foreach ($iItem->overigeadressen as $adres) self::CreateStudentAddress($item->id, $adres, false);
                    foreach ($iItem->comnrs as $comnr) self::CreateStudentNumber($item->id, $comnr);
                    foreach ($iItem->emails as $email) self::CreateStudentEmail($item->id, $email);
                    foreach ($iItem->bankrek as $bankr) self::CreateStudentBank($item->id, $bankr);
                    foreach ($iItem->relaties as $relatie) self::CreateStudentRelation($item->id, $relatie);
                } catch (\Exception $e) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $_error_ = true;
                    continue;
                }
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return !$_error_;
    }

    private static function StudentPhotos($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import students pictures...");
        $_error = false;
        $informatRepo = new Student;
        $informatPhotoRepo = new StudentPhoto;
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/student");

        foreach (_INSTITUTES_ as $institute) {
            $import = $institute->linked->school->import || $institute->linked->school->linked->parentSchool->import;

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat} - {$institute->linked->school->name} (import: " .  (!$import ? "NO" : "YES") . ")");
            if (!$import) continue;

            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                $photo = $informatPhotoRepo->get($institute->numberNewFormat, $iItem->persoonId, true);
                if (Strings::isBlank($photo['foto'])) continue;

                try {
                    $b64Photo = base64_decode($photo['foto']);

                    if (FileSystem::PathExists(LOCATION_IMAGE . "/informat/student/{$iItem->persoonId}.jpg")) {
                        $currentPhoto = file_get_contents(LOCATION_IMAGE . "/informat/student/{$iItem->persoonId}.jpg");

                        if (!Strings::equal($b64Photo, $currentPhoto)) {
                            FileSystem::WriteFile(LOCATION_IMAGE . "/informat/student/{$iItem->persoonId}.jpg", $b64Photo);
                        }
                    } else FileSystem::WriteFile(LOCATION_IMAGE . "/informat/student/{$iItem->persoonId}.jpg", $b64Photo);

                    $photo = null;
                } catch (\Exception $e) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error;
    }

    private static function Registrations($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import registrations...");
        $_error_ = false;

        $informatRepo = new Registration;
        $studentRepo = new RepositoryInformatStudent;

        foreach (_INSTITUTES_ as $institute) {
            $import = $institute->linked->school->import || $institute->linked->school->linked->parentSchool->import;

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat} - {$institute->linked->school->name} (import: " .  (!$import ? "NO" : "YES") . ")");
            if (!$import) continue;

            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);
            $repo = new RepositoryInformatRegistration;

            foreach ($iItems as $iItem) {
                try {
                    if (Clock::at($iItem->begindatum)->format("m-d") === "09-01") $iItem->begindatum = Clock::at($iItem->begindatum)->format("Y-08-01");
                    if (!is_null($iItem->einddatum) && Clock::at($iItem->einddatum)->format("m-d") === "06-30") $iItem->einddatum = Clock::at($iItem->einddatum)->format("Y-07-31");

                    $item = $repo->getByInformatId($iItem->pInschr) ?? $repo->getByInformatGuid($iItem->inschrijvingsId) ?? new InformatRegistration;
                    $item->informatId = Strings::trimToNull($iItem->pInschr);
                    $item->informatGuid = Strings::trimToNull($iItem->inschrijvingsId);
                    $item->informatStudentId = Strings::trimToNull($studentRepo->getByInformatGuid($iItem->persoonId)->id);
                    $item->schoolInstituteId = Strings::trimToNull($institute->id);
                    $item->basenumber = Strings::trimToNull($iItem->stamnr);
                    $item->departmentCode = Strings::trimToNull($iItem->afdCode);
                    $item->grade = Strings::trimToNull($iItem->graad);
                    $item->year = Strings::trimToNull($iItem->leerjaar);
                    $item->start = Strings::trimToNull($iItem->begindatum);
                    $item->end = Strings::trimToNull($iItem->einddatum);
                    $item->status = Strings::trimToNull($iItem->status);
                    $item->current = ($iItem->status == 0 && Clock::now()->isAfterOrEqualTo(Clock::at($schoolyear->start)) && Clock::at($schoolyear->start)->isAfterOrEqualTo(Clock::at($iItem->begindatum)) && (is_null($iItem->einddatum) || Clock::at($schoolyear->end)->isBeforeOrEqualTo(Clock::at($iItem->einddatum))));

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->inschrKlassen as $inschr) {
                        $classgroupId = self::CreateClassGroup($institute->id, $inschr, $iItem->nrAdmgrp, $item->departmentCode, $item->grade, $item->year);
                        self::CreateRegistrationClass($item->id, $classgroupId, $inschr, $schoolyear);

                        foreach ($inschr->klassenleraars as $teacher) self::CreateClassGroupTeacher($classgroupId, $teacher['persoonId']);
                    }
                } catch (\Exception $e) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    private static function Employees($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import employees...");
        $_error_ = false;

        $informatRepo = new Employee;
        $cRepo = new Country;
        $repo = new RepositoryInformatEmployee;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Removing institute ID from employees and disable them all temp...");
        foreach ($repo->get() as $employee) {
            $employee->instituteId = 0;
            $employee->active = 0;
            $repo->set($employee);
        }


        foreach (_INSTITUTES_ as $institute) {
            $import = $institute->linked->school->import || $institute->linked->school->linked->parentSchool->import;

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat} - {$institute->linked->school->name} (import: " .  (!$import ? "NO" : "YES") . ")");
            if (!$import) continue;

            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$iItem->pPersoon} ({$iItem->personId}) - {$iItem->naam} {$iItem->voornaam}");

                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->personId) ?? new InformatEmployee;
                    $item->informatId = Strings::trimToNull($iItem->pPersoon);
                    $item->informatGuid = Strings::trimToNull($iItem->personId);
                    $item->name = Strings::trimToNull($iItem->naam);
                    $item->firstName = Strings::trimToNull($iItem->voornaam);
                    $item->extraFirstName = Strings::trimToNull($iItem->bijkomendeVoornamen);
                    $item->basenumber = Strings::trimToNull($iItem->stamnr);
                    $item->sex = (Strings::equalsIgnoreCase($iItem->geslacht, "m") ? "M" : "F");
                    $item->birthDate = $iItem->geboortedatum;
                    $item->birthPlace = Strings::trimToNull($iItem->geboorteplaats);
                    $item->birthCountryId = $cRepo->getByNisCode(General::removeLeadingZero($iItem->geboortelandCode))->id;
                    $item->nationalityId = $cRepo->getByNisCode(General::removeLeadingZero($iItem->nationaliteitCode))->id;
                    $item->insz = Strings::trimToNull($iItem->rijksregisternr);
                    $item->bis = Strings::trimToNull($iItem->bisnr);
                    $item->iban = Strings::trimToNull($iItem->bank->iban);
                    $item->bic = Strings::trimToNull($iItem->bank->bic);
                    $item->active = $iItem->isActive;
                    $item->instituteId = $institute->id;

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->adressen as $adres) self::CreateEmployeeAddress($item->id, $adres);
                    foreach ($iItem->comnrs as $comnr) self::CreateEmployeeNumber($item->id, $comnr);
                    foreach ($iItem->emailadressen as $email) self::CreateEmployeeEmail($item->id, $email);
                } catch (\Exception $e) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $_error_ = true;
                    continue;
                }
            }
            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return !$_error_;
    }

    private static function EmployeeOwnfields($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import employees own fields...");
        $_error_ = false;

        $informatRepo = new RepositoryEmployeeOwnfield;
        $employeeRepo = new RepositoryInformatEmployee;
        $repo = new InformatEmployeeOwnfield;
        $repo->delete();

        foreach (_INSTITUTES_ as $institute) {
            $import = $institute->linked->school->import || $institute->linked->school->linked->parentSchool->import;

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat} - {$institute->linked->school->name} (import: " .  (!$import ? "NO" : "YES") . ")");
            if (!$import) continue;

            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$iItem->personId}; Item: {$iItem->naam} = {$iItem->waarde}");

                try {
                    $employeeId = $employeeRepo->getByInformatGuid($iItem->personId)->id;
                    $item = $repo->getByInformatGuidAndEmployeeId($iItem->vvId, $employeeId) ?? new EmployeeOwnfield;
                    $item->informatEmployeeId = $employeeId;
                    $item->informatGuid = $iItem->vvId;
                    $item->name = Strings::trimToNull($iItem->naam);
                    $item->value = Strings::trimToNull($iItem->waarde);
                    $item->type = Strings::trimToNull($iItem->dataType);
                    $item->section = $iItem->rubriek;

                    $repo->set($item);
                } catch (\Exception $e) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $_error_ = true;
                    continue;
                }
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return !$_error_;
    }

    private static function EmployeePhotos($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import employees pictures...");
        $_error_ = false;

        $informatRepo = new EmployeePhoto;
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/employee");

        foreach (_INSTITUTES_ as $institute) {
            $iItems = $informatRepo->get($schoolyear->name, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                if (Strings::isBlank($iItem->photo)) continue;

                try {
                    if (FileSystem::PathExists(LOCATION_IMAGE . "/informat/employee/{$iItem->personId}.jpg")) {
                        $currentPhoto = file_get_contents(LOCATION_IMAGE . "/informat/employee/{$iItem->personId}.jpg");

                        if (!Strings::equal($iItem->photo, $currentPhoto)) {
                            FileSystem::WriteFile(LOCATION_IMAGE . "/informat/employee/{$iItem->personId}.jpg", $iItem->photo);
                        }
                    } else FileSystem::WriteFile(LOCATION_IMAGE . "/informat/employee/{$iItem->personId}.jpg", $iItem->photo);
                } catch (\Exception $e) {
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    // Sub functions
    private static function CreateStudentAddress($studentId, $adres, $domicile = false)
    {
        $addressRepo = new InformatStudentAddress;

        $address = $addressRepo->getByInformatId($adres->pAdres) ?? $addressRepo->getByInformatGuid($adres->adresId) ?? new StudentAddress;
        $address->informatStudentId = $studentId;
        $address->informatId = $adres->pAdres;
        $address->informatGuid = $adres->adresId;
        $address->street = Strings::trimToNull($adres->straat);
        $address->number = Strings::trimToNull($adres->nr);
        $address->bus = Strings::trimToNull($adres->bus);
        $address->zipcode = Strings::trimToNull($adres->postcode);
        $address->city = Strings::trimToNull($adres->gemeente);
        $address->countryId = (new Country)->getByNisCode(General::removeLeadingZero($adres->landCode))->id;
        $address->domicile = is_null($domicile) ? $address->domicile : $domicile;

        $addressRepo->set($address);
    }

    private static function CreateStudentNumber($studentId, $comnr)
    {
        $numberRepo = new InformatStudentNumber;
        $number = $numberRepo->getByInformatId($comnr->pComnr) ?? new StudentNumber;
        $number->informatStudentId = $studentId;
        $number->informatId = $comnr->pComnr;
        $number->number = Strings::trimToNull($comnr->nr);
        $number->type = Strings::trimToNull($comnr->type);
        $number->category = Strings::trimToNull($comnr->soort);

        $numberRepo->set($number);
    }

    private static function CreateStudentEmail($studentId, $email)
    {
        $emailRepo = new InformatStudentEmail;
        $mail = $emailRepo->getByInformatId($email->pEmail) ?? new StudentEmail;
        $mail->informatStudentId = $studentId;
        $mail->informatId = $email->pEmail;
        $mail->email = Strings::trimToNull($email->email);
        $mail->type = Strings::trimToNull($email->type);
        $mail->communication = $email->schoolcom;

        $emailRepo->set($mail);
    }

    private static function CreateStudentBank($studentId, $bankr)
    {
        $bankRepo = new InformatStudentBank;
        $bank = $bankRepo->getByInformatStudentIdAndIban($studentId, $bankr->iban) ?? new StudentBank;
        $bank->informatStudentId = $studentId;
        $bank->type = Strings::trimToNull($bankr->type);
        $bank->iban = Strings::trimToNull($bankr->iban);
        $bank->bic = Strings::trimToNull($bankr->bic);

        $bankRepo->set($bank);
    }

    private static function CreateStudentRelation($studentId, $relatie)
    {
        $relationRepo = new InformatStudentRelation;
        $relation = $relationRepo->getByInformatId($relatie->pRelatie) ?? new StudentRelation;
        $relation->informatStudentId = $studentId;
        $relation->informatId = $relatie->pRelatie;
        $relation->informatGuid = $relatie->relatieId;
        $relation->type = Strings::trimToNull($relatie->type);
        $relation->name = Strings::trimToNull($relatie->naam);
        $relation->firstName = Strings::trimToNull($relatie->voornaam);
        $relation->insz = Strings::trimToNull($relatie->insz);
        $relation->birthDate = $relatie->geboortedatum;
        $relation->sex = (Strings::equalsIgnoreCase("m", $relatie->geslacht) ? "M" : (Strings::equalsIgnoreCase("v", $relatie->geslacht) ? "F" : "X"));
        $relation->nationalityId = (new Country)->getByNisCode(General::removeLeadingZero($relatie->nationaliteitCode))->id;
        $relation->job = Strings::trimToNull($relatie->beroep);
        $relation->civilStatus = Strings::trimToNull($relatie->burgerlijkeStand);
        $relation->rank = Strings::trimToNull($relatie->lpv);
        $relationRepo->set($relation);

        foreach ($relatie->adressen as $adres) self::CreateStudentAddress($studentId, General::convertToObject($adres), null);
        foreach ($relatie->comnrs as $comnr) self::CreateStudentNumber($studentId, General::convertToObject($comnr));
        foreach ($relatie->emails as $email) self::CreateStudentEmail($studentId, General::convertToObject($email));
    }

    private static function CreateClassGroup($instituteId, $inschr, $administrativeGroupCode, $departmentCode, $grade, $year)
    {
        $classgroupRepo = new InformatClassGroup;
        $classgroup = $classgroupRepo->getByInformatId($inschr->pKlas) ?? $classgroupRepo->getByInformatGuid($inschr->klasId) ?? new ClassGroup;
        $classgroup->informatId = $inschr->pKlas;
        $classgroup->informatGuid = $inschr->klasId;
        $classgroup->schoolInstituteId = $instituteId;
        $classgroup->schoolyear = (new Schoolyear)->getByDate($inschr->begindatum)->name;
        $classgroup->administrativeGroupCode = Strings::trimToNull($administrativeGroupCode);
        $classgroup->departmentCode = Strings::trimToNull($departmentCode);
        $classgroup->grade = Strings::trimToNull($grade);
        $classgroup->year = Strings::trimToNull($year);
        $classgroup->code = Strings::trimToNull($inschr->klasCode);
        $classgroup->name = Strings::trimToNull($inschr->klas);
        $classgroup->type = $inschr->groepType == 0 ? 'C' : 'S';

        $nId = $classgroupRepo->set($classgroup);
        if (!$classgroup->id) $classgroup->id = $nId;

        return $classgroup->id;
    }

    private static function CreateRegistrationClass($registrationId, $classgroupId, $inschr, $schoolyear)
    {
        if (Clock::at($inschr->begindatum)->format("m-d") === "09-01") $inschr->begindatum = Clock::at($inschr->begindatum)->format("Y-08-01");
        if (!is_null($inschr->einddatum) && Clock::at($inschr->einddatum)->format("m-d") === "06-30") $inschr->einddatum = Clock::at($inschr->einddatum)->format("Y-07-31");

        $registrationClassRepo = new InformatRegistrationClass;
        $registrationClass = $registrationClassRepo->getByInformatGuid($inschr->inschrKlasId) ?? new RegistrationClass;
        $registrationClass->informatGuid = $inschr->inschrKlasId;
        $registrationClass->informatRegistrationId = $registrationId;
        $registrationClass->informatClassGroupId = $classgroupId;
        $registrationClass->rank = $inschr->klasnummer;
        $registrationClass->start = $inschr->begindatum;
        $registrationClass->end = $inschr->einddatum;
        $registrationClass->current = (Clock::now()->isAfterOrEqualTo(Clock::at($schoolyear->start)) && Clock::now()->isAfterOrEqualTo(Clock::at($inschr->begindatum)) && (is_null($inschr->einddatum) || Clock::now()->isBeforeOrEqualTo(Clock::at($inschr->einddatum))));
        $registrationClassRepo->set($registrationClass);
    }

    private static function CreateClassGroupTeacher($classgroupId, $employeeId)
    {
        $classgroupTeacherRepo = new ClassGroupTeacher;
        $informatEmployeeRepo = new RepositoryInformatEmployee;
        $informatEmployee = $informatEmployeeRepo->getByInformatGuid($employeeId);
        if (!$informatEmployee) return;

        $classgroupTeacher = $classgroupTeacherRepo->getByInformatClassgroupIdAndInformatEmployeeId($classgroupId, $informatEmployee->id) ?? new InformatClassgroupTeacher;
        $classgroupTeacher->informatClassGroupId = $classgroupId;
        $classgroupTeacher->informatEmployeeId = $informatEmployee->id;
        $classgroupTeacherRepo->set($classgroupTeacher);
    }

    private static function CreateEmployeeAddress($employeeId, $adres)
    {
        $addressRepo = new InformatEmployeeAddress;

        $address = $addressRepo->getByInformatGuid($adres->id) ?? new EmployeeAddress;
        $address->informatEmployeeId = $employeeId;
        $address->informatGuid = $adres->id;
        $address->street = Strings::trimToNull($adres->straat);
        $address->number = Strings::trimToNull($adres->nummer);
        $address->bus = Strings::trimToNull($adres->bus);
        $address->zipcode = Strings::trimToNull($adres->postcode);
        $address->city = Strings::trimToNull($adres->gemeente);
        $address->countryId = (new Country)->getByNisCode(General::removeLeadingZero($adres->landCode))->id;
        $address->current = $adres->isDomicilie;

        $addressRepo->set($address);
    }

    private static function CreateEmployeeNumber($employeeId, $comnr)
    {
        $numberRepo = new InformatEmployeeNumber;

        $number = $numberRepo->getByInformatGuid($comnr->id) ?? new EmployeeNumber;
        $number->informatEmployeeId = $employeeId;
        $number->informatGuid = $comnr->id;
        $number->number = Strings::trimToNull($comnr->nr);
        $number->type = Strings::trimToNull($comnr->type);
        $number->category = Strings::trimToNull($comnr->soort);

        $numberRepo->set($number);
    }

    private static function CreateEmployeeEmail($employeeId, $email)
    {
        $emailRepo = new InformatEmployeeEmail;
        $mail = $emailRepo->getByInformatGuid($email->id) ?? new EmployeeEmail;
        $mail->informatEmployeeId = $employeeId;
        $mail->informatGuid = $email->id;
        $mail->email = Strings::trimToNull($email->email);
        $mail->type = Strings::trimToNull($email->type);

        $emailRepo->set($mail);
    }
}
