<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Router\Helpers;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Informat\Repository\Student;
use Informat\Repository\Employee;
use Informat\Repository\Registration;
use Informat\Repository\StudentPhoto;
use Informat\Repository\EmployeePhoto;
use Database\Object\Informat\ClassGroup;
use Database\Repository\General\Country;
use Database\Object\Informat\StudentBank;
use Database\Repository\School\Institute;
use Database\Object\Informat\StudentEmail;
use Database\Object\Informat\EmployeeEmail;
use Database\Object\Informat\StudentNumber;
use Database\Object\Informat\EmployeeNumber;
use Database\Object\Informat\StudentAddress;
use Database\Object\Informat\EmployeeAddress;
use Database\Object\Informat\StudentRelation;
use Database\Object\Informat\EmployeeOwnfield;
use Database\Object\Informat\RegistrationClass;
use Database\Object\Informat\Student as InformatStudent;
use Database\Object\Informat\Employee as InformatEmployee;
use Database\Object\Informat\Registration as InformatRegistration;
use Database\Repository\Informat\ClassGroup as InformatClassGroup;
use Database\Repository\Informat\StudentBank as InformatStudentBank;
use Database\Repository\Informat\Student as RepositoryInformatStudent;
use Database\Repository\Informat\StudentEmail as InformatStudentEmail;
use Informat\Repository\EmployeeOwnfield as RepositoryEmployeeOwnfield;
use Database\Repository\Informat\Employee as RepositoryInformatEmployee;
use Database\Repository\Informat\EmployeeEmail as InformatEmployeeEmail;
use Database\Repository\Informat\StudentNumber as InformatStudentNumber;
use Database\Repository\Informat\EmployeeNumber as InformatEmployeeNumber;
use Database\Repository\Informat\StudentAddress as InformatStudentAddress;
use Database\Repository\Informat\EmployeeAddress as InformatEmployeeAddress;
use Database\Repository\Informat\StudentRelation as InformatStudentRelation;
use Database\Repository\Informat\EmployeeOwnfield as InformatEmployeeOwnfield;
use Database\Repository\Informat\Registration as RepositoryInformatRegistration;
use Database\Repository\Informat\RegistrationClass as InformatRegistrationClass;
use Ouzo\Utilities\Arrays;

abstract class Informat
{
    static public function Import()
    {
        define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
        define("_LOGLOCATION_", "cron/informat");
        Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . General::getSchoolyear());

        if (Helpers::url()->hasParam("image")) {
            $studentPhoto = self::StudentPhotos();
            $employeePhoto = self::EmployeePhotos();

            Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

            return ($studentPhoto && $employeePhoto);
        } else {
            $schoolyear = General::getSchoolyear();
            if (Helpers::url()->hasParam("nextSchoolyear")) $schoolyear = General::getSchoolyear(Clock::now()->plusYears(1)->format("Y-m-d"));

            $student = self::Students($schoolyear);
            $registration = self::Registrations($schoolyear);
            $employee = self::Employees($schoolyear);
            $employeeOwnfield = self::EmployeeOwnfields($schoolyear);

            Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

            return ($student && $registration && $employee && $employeeOwnfield);
        }
    }

    // Main Functions
    static private function Students($schoolyear = null)
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import students...");
        $_error_ = false;

        $informatRepo = new Student;
        $schoolInstitutes = (new Institute)->get();
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/student");

        foreach ($schoolInstitutes as $institute) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat}");
            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);

            $repo = new RepositoryInformatStudent;

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Student: {$iItem->pPersoon} ({$iItem->persoonId}) - {$iItem->naam} {$iItem->voornaam}");

                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->persoonId) ?? new InformatStudent;
                    $item->informatId = $iItem->pPersoon;
                    $item->informatGuid = $iItem->persoonId;
                    $item->name = trim($iItem->naam);
                    $item->firstName = trim($iItem->voornaam);
                    $item->sex = (Strings::equalsIgnoreCase($iItem->geslacht, "m") ? "M" : "F");
                    $item->birthDate = $iItem->geboortedatum;
                    $item->birthPlace = $iItem->geboorteplaats;
                    $item->isdn = $iItem->rijksregisternr ?: $iItem->bisnr ?: null;

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->adressen as $adres) self::CreateStudentAddress($item->id, $adres);
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
        $schoolInstitutes = (new Institute)->get();
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/student");

        foreach ($schoolInstitutes as $institute) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat}");
            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);

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
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to registrations...");
        $_error_ = false;

        $informatRepo = new Registration;
        $studentRepo = new RepositoryInformatStudent;
        $schoolInstitutes = (new Institute)->get();

        foreach ($schoolInstitutes as $institute) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat}");
            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);
            $repo = new RepositoryInformatRegistration;

            foreach ($iItems as $iItem) {
                try {
                    if (!is_null($iItem->einddatum) && Clock::at($iItem->einddatum)->format("m-d") === "06-30") $iItem->einddatum = Clock::at($iItem->einddatum)->format("Y-08-31");

                    $item = $repo->getByInformatId($iItem->pInschr) ?? $repo->getByInformatGuid($iItem->inschrijvingsId) ?? new InformatRegistration;
                    $item->informatId = $iItem->pInschr;
                    $item->informatGuid = $iItem->inschrijvingsId;
                    $item->informatStudentId = $studentRepo->getByInformatGuid($iItem->persoonId)->id;
                    $item->schoolInstituteId = $institute->id;
                    $item->basenumber = $iItem->stamnr;
                    $item->departmentCode = $iItem->afdCode;
                    $item->grade = $iItem->graad;
                    $item->year = $iItem->leerjaar;
                    $item->start = $iItem->begindatum;
                    $item->end = $iItem->einddatum;
                    $item->status = $iItem->status;
                    $item->current = ($iItem->status == 0 && Clock::now()->isAfterOrEqualTo(Clock::at($iItem->begindatum)) && (is_null($iItem->einddatum) || Clock::now()->isBeforeOrEqualTo(Clock::at($iItem->einddatum))));

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->inschrKlassen as $inschr) {
                        $classgroupId = self::CreateClassGroup($institute->id, $inschr, $iItem->nrAdmgrp, $item->departmentCode, $item->grade, $item->year);
                        self::CreateRegistrationClass($item->id, $classgroupId, $inschr);
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
        $schoolInstitutes = (new Institute)->get();
        $cRepo = new Country;

        foreach ($schoolInstitutes as $institute) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat}");

            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);
            $repo = new RepositoryInformatEmployee;

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$iItem->pPersoon} ({$iItem->personId}) - {$iItem->naam} {$iItem->voornaam}");

                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->personId) ?? new InformatEmployee;
                    $item->informatId = $iItem->pPersoon;
                    $item->informatGuid = $iItem->personId;
                    $item->name = trim($iItem->naam);
                    $item->firstName = trim($iItem->voornaam);
                    $item->extraFirstName = trim($iItem->bijkomendeVoornamen);
                    $item->basenumber = $iItem->stamnr;
                    $item->sex = (Strings::equalsIgnoreCase($iItem->geslacht, "m") ? "M" : "F");
                    $item->birthDate = $iItem->geboortedatum;
                    $item->birthPlace = $iItem->geboorteplaats;
                    $item->birthCountryId = $cRepo->getByNisCode(General::removeLeadingZero($iItem->geboortelandCode))->id;
                    $item->nationalityId = $cRepo->getByNisCode(General::removeLeadingZero($iItem->nationaliteitCode))->id;
                    $item->insz = $iItem->rijksregisternr;
                    $item->bis = $iItem->bisnr;
                    $item->iban = $iItem->bank->iban;
                    $item->bic = $iItem->bank->bic;
                    $item->active = $iItem->isActive;

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
        $schoolInstitutes = (new Institute)->get();

        foreach ($schoolInstitutes as $institute) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Institute: {$institute->numberNewFormat}");
            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$iItem->personId}; Item: {$iItem->naam} - {$iItem->waarde}");

                try {
                    $employeeId = $employeeRepo->getByInformatGuid($iItem->personId)->id;
                    $item = $repo->getByInformatGuidAndEmployeeId($iItem->vvId, $employeeId) ?? new EmployeeOwnfield;
                    $item->informatEmployeeId = $employeeId;
                    $item->informatGuid = $iItem->vvId;
                    $item->name = $iItem->naam;
                    $item->value = $iItem->waarde;
                    $item->type = $iItem->dataType;
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
        $schoolInstitutes = (new Institute)->get();
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/employee");

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->sourceId, $schoolyear, $institute->numberNewFormat);

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
    private static function CreateStudentAddress($studentId, $adres)
    {
        $addressRepo = new InformatStudentAddress;

        $address = $addressRepo->getByInformatId($adres->pAdres) ?? $addressRepo->getByInformatGuid($adres->adresId) ?? new StudentAddress;
        $address->informatStudentId = $studentId;
        $address->informatId = $adres->pAdres;
        $address->informatGuid = $adres->adresId;
        $address->street = $adres->straat;
        $address->number = $adres->nr;
        $address->bus = $adres->bus;
        $address->zipcode = $adres->postcode;
        $address->city = $adres->gemeente;
        $address->countryId = (new Country)->getByNisCode(General::removeLeadingZero($adres->landCode))->id;

        $addressRepo->set($address);
    }

    private static function CreateStudentNumber($studentId, $comnr)
    {
        $numberRepo = new InformatStudentNumber;
        $number = $numberRepo->getByInformatId($comnr->pComnr) ?? new StudentNumber;
        $number->informatStudentId = $studentId;
        $number->informatId = $comnr->pComnr;
        $number->number = $comnr->nr;
        $number->type = $comnr->type;
        $number->category = $comnr->soort;

        $numberRepo->set($number);
    }

    private static function CreateStudentEmail($studentId, $email)
    {
        $emailRepo = new InformatStudentEmail;
        $mail = $emailRepo->getByInformatId($email->pEmail) ?? new StudentEmail;
        $mail->informatStudentId = $studentId;
        $mail->informatId = $email->pEmail;
        $mail->email = $email->email;
        $mail->type = $email->type;

        $emailRepo->set($mail);
    }

    private static function CreateStudentBank($studentId, $bankr)
    {
        $bankRepo = new InformatStudentBank;
        $bank = $bankRepo->getByInformatStudentIdAndIban($studentId, $bankr->iban) ?? new StudentBank;
        $bank->informatStudentId = $studentId;
        $bank->type = $bankr->type;
        $bank->iban = $bankr->iban;
        $bank->bic = $bankr->bic;

        $bankRepo->set($bank);
    }

    private static function CreateStudentRelation($studentId, $relatie)
    {
        $relationRepo = new InformatStudentRelation;
        $relation = $relationRepo->getByInformatId($relatie->pRelatie) ?? new StudentRelation;
        $relation->informatStudentId = $studentId;
        $relation->informatId = $relatie->pRelatie;
        $relation->informatGuid = $relatie->relatieId;
        $relation->type = $relatie->type;
        $relation->name = $relatie->naam;
        $relation->firstName = $relatie->voornaam;
        $relation->insz = $relatie->insz;
        $relation->birthDate = $relatie->geboortedatum;
        $relation->sex = (Strings::equalsIgnoreCase("m", $relatie->geslacht) ? "M" : (Strings::equalsIgnoreCase("v", $relatie->geslacht) ? "F" : "X"));
        $relation->nationalityId = (new Country)->getByNisCode(General::removeLeadingZero($relatie->nationaliteitCode))->id;
        $relation->job = $relatie->beroep;
        $relation->civilStatus = $relatie->burgerlijkeStand;
        $relation->rank = $relatie->lpv;
        $relationRepo->set($relation);

        foreach ($relatie->adressen as $adres) self::CreateStudentAddress($studentId, General::convertToObject($adres));
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
        $classgroup->schoolyear = General::getSchoolyear($inschr->begindatum);
        $classgroup->administrativeGroupCode = $administrativeGroupCode;
        $classgroup->departmentCode = $departmentCode;
        $classgroup->grade = $grade;
        $classgroup->year = $year;
        $classgroup->code = trim($inschr->klasCode);
        $classgroup->name = trim($inschr->klas);
        $classgroup->type = $inschr->groepType == 0 ? 'C' : 'S';

        $nId = $classgroupRepo->set($classgroup);
        if (!$classgroup->id) $classgroup->id = $nId;

        return $classgroup->id;
    }

    private static function CreateRegistrationClass($registrationId, $classgroupId, $inschr)
    {
        if (!is_null($inschr->einddatum) && Clock::at($inschr->einddatum)->format("m-d") === "06-30") $inschr->einddatum = Clock::at($inschr->einddatum)->format("Y-08-31");

        $registrationClassRepo = new InformatRegistrationClass;
        $registrationClass = $registrationClassRepo->getByInformatGuid($inschr->inschrKlasId) ?? new RegistrationClass;
        $registrationClass->informatGuid = $inschr->inschrKlasId;
        $registrationClass->informatRegistrationId = $registrationId;
        $registrationClass->informatClassGroupId = $classgroupId;
        $registrationClass->rank = $inschr->klasnummer;
        $registrationClass->start = $inschr->begindatum;
        $registrationClass->end = $inschr->einddatum;
        $registrationClass->current = (Clock::now()->isAfterOrEqualTo(Clock::at($inschr->begindatum)) && (is_null($inschr->einddatum) || Clock::now()->isBeforeOrEqualTo(Clock::at($inschr->einddatum))));
        $registrationClassRepo->set($registrationClass);
    }

    private static function CreateEmployeeAddress($employeeId, $adres)
    {
        $addressRepo = new InformatEmployeeAddress;

        $address = $addressRepo->getByInformatGuid($adres->id) ?? new EmployeeAddress;
        $address->informatEmployeeId = $employeeId;
        $address->informatGuid = $adres->id;
        $address->street = $adres->straat;
        $address->number = $adres->nummer;
        $address->bus = $adres->bus;
        $address->zipcode = $adres->postcode;
        $address->city = $adres->gemeente;
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
        $number->number = $comnr->nr;
        $number->type = $comnr->type;
        $number->category = $comnr->soort;

        $numberRepo->set($number);
    }

    private static function CreateEmployeeEmail($employeeId, $email)
    {
        $emailRepo = new InformatEmployeeEmail;
        $mail = $emailRepo->getByInformatGuid($email->id) ?? new EmployeeEmail;
        $mail->informatEmployeeId = $employeeId;
        $mail->informatGuid = $email->id;
        $mail->email = $email->email;
        $mail->type = $email->type;

        $emailRepo->set($mail);
    }
}
