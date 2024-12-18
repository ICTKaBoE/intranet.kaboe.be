<?php

namespace Controllers\API\Cron;

use Database\Object\Informat\ClassGroup;
use Database\Object\Informat\Employee as InformatEmployee;
use Database\Object\Informat\EmployeeAddress;
use Database\Object\Informat\EmployeeEmail;
use Database\Object\Informat\EmployeeNumber;
use Database\Object\Informat\EmployeeOwnfield;
use Database\Object\Informat\Registration as InformatRegistration;
use Database\Object\Informat\RegistrationClass;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Informat\Repository\Student;
use Informat\Repository\Registration;
use Informat\Repository\StudentPhoto;
use Database\Repository\SchoolInstitute;
use Database\Object\Informat\StudentBank;
use Database\Object\Informat\StudentEmail;
use Database\Object\Informat\StudentNumber;
use Database\Object\Informat\StudentAddress;
use Database\Object\Informat\StudentRelation;
use Database\Object\Informat\Student as InformatStudent;
use Database\Repository\Country;
use Database\Repository\Informat\ClassGroup as InformatClassGroup;
use Database\Repository\Informat\Employee as RepositoryInformatEmployee;
use Database\Repository\Informat\EmployeeAddress as InformatEmployeeAddress;
use Database\Repository\Informat\EmployeeEmail as InformatEmployeeEmail;
use Database\Repository\Informat\EmployeeNumber as InformatEmployeeNumber;
use Database\Repository\Informat\EmployeeOwnfield as InformatEmployeeOwnfield;
use Database\Repository\Informat\Registration as RepositoryInformatRegistration;
use Database\Repository\Informat\RegistrationClass as InformatRegistrationClass;
use Database\Repository\Informat\StudentBank as InformatStudentBank;
use Database\Repository\Informat\Student as RepositoryInformatStudent;
use Database\Repository\Informat\StudentEmail as InformatStudentEmail;
use Database\Repository\Informat\StudentNumber as InformatStudentNumber;
use Database\Repository\Informat\StudentAddress as InformatStudentAddress;
use Database\Repository\Informat\StudentRelation as InformatStudentRelation;
use Helpers\General;
use Informat\Repository\Employee;
use Informat\Repository\EmployeeOwnfield as RepositoryEmployeeOwnfield;
use Informat\Repository\EmployeePhoto;

abstract class Informat
{
    static public function Import()
    {
        $student = self::Students();
        $registration = self::Registrations();
        $employee = self::Employees();
        $employeeOwnfield = self::EmployeeOwnfields();
        $employeePhoto = self::EmployeePhotos();

        return ($student && $registration && $employee && $employeeOwnfield);
        // return true;
    }

    // Main Functions
    static private function Students()
    {
        $_error_ = false;

        $informatRepo = new Student;
        $informatPhotoRepo = new StudentPhoto;
        $schoolInstitutes = (new SchoolInstitute)->get();
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/student");

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->numberNewFormat);

            $repo = new RepositoryInformatStudent;

            foreach ($iItems as $iItem) {
                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->persoonId) ?? new InformatStudent;
                    $item->informatId = $iItem->pPersoon;
                    $item->informatGuid = $iItem->persoonId;
                    $item->name = $iItem->naam;
                    $item->firstName = $iItem->voornaam;
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

                    // $photo = $informatPhotoRepo->get($institute->numberNewFormat, $item->informatGuid, true);
                    // FileSystem::WriteFile(LOCATION_IMAGE . "/informat/student/{$item->informatGuid}.jpg", $photo['foto']);
                } catch (\Exception) {
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    private static function Registrations()
    {
        $_error_ = false;

        $informatRepo = new Registration;
        $studentRepo = new RepositoryInformatStudent;
        $schoolInstitutes = (new SchoolInstitute)->get();

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->numberNewFormat);
            $repo = new RepositoryInformatRegistration;

            foreach ($iItems as $iItem) {
                try {
                    $item = $repo->getByInformatId($iItem->pInschr) ?? $repo->getByInformatGuid($iItem->inschrijvingsId) ?? new InformatRegistration;
                    $item->informatId = $iItem->pInschr;
                    $item->informatGuid = $iItem->inschrijvingsId;
                    $item->informatStudentId = $studentRepo->getByInformatGuid($iItem->persoonId)->id;
                    $item->schoolInstituteId = $institute->id;
                    $item->basenumber = $iItem->stamnr;
                    $item->start = $iItem->begindatum;
                    $item->end = $iItem->einddatum;
                    $item->status = $iItem->status;

                    $nId = $repo->set($item);
                    if (!$item->id) $item->id = $nId;

                    foreach ($iItem->inschrKlassen as $inschr) {
                        $classgroupId = self::CreateClassGroup($institute->id, $inschr);
                        self::CreateRegistrationClass($item->id, $classgroupId, $inschr);
                    }
                } catch (\Exception $e) {
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    private static function Employees()
    {
        $_error_ = false;

        $informatRepo = new Employee;
        $schoolInstitutes = (new SchoolInstitute)->get();
        $cRepo = new Country;

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->numberNewFormat);
            $repo = new RepositoryInformatEmployee;

            foreach ($iItems as $iItem) {
                try {
                    $item = $repo->getByInformatId($iItem->pPersoon) ?? $repo->getByInformatGuid($iItem->personId) ?? new InformatEmployee;
                    $item->informatId = $iItem->pPersoon;
                    $item->informatGuid = $iItem->personId;
                    $item->name = $iItem->naam;
                    $item->firstName = $iItem->voornaam;
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
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    private static function EmployeeOwnfields()
    {
        $_error_ = false;

        $informatRepo = new RepositoryEmployeeOwnfield;
        $employeeRepo = new RepositoryInformatEmployee;
        $schoolInstitutes = (new SchoolInstitute)->get();

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->numberNewFormat);
            $repo = new InformatEmployeeOwnfield;

            foreach ($iItems as $iItem) {
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
                    $_error_ = true;
                    continue;
                }
            }
        }

        return !$_error_;
    }

    private static function EmployeePhotos()
    {
        $_error_ = false;

        $informatRepo = new EmployeePhoto;
        $schoolInstitutes = (new SchoolInstitute)->get();
        FileSystem::CreateFolder(LOCATION_IMAGE . "/informat/employee");

        foreach ($schoolInstitutes as $institute) {
            $iItems = $informatRepo->get($institute->numberNewFormat);

            foreach ($iItems as $iItem) {
                try {
                    FileSystem::WriteFile(LOCATION_IMAGE . "/informat/employee/{$iItem->personId}.jpg", $iItem->photo);
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

    private static function CreateClassGroup($instituteId, $inschr)
    {
        $classgroupRepo = new InformatClassGroup;
        $classgroup = $classgroupRepo->getByInformatId($inschr->pKlas) ?? $classgroupRepo->getByInformatGuid($inschr->klasId) ?? new ClassGroup;
        $classgroup->informatId = $inschr->pKlas;
        $classgroup->informatGuid = $inschr->klasId;
        $classgroup->schoolInstituteId = $instituteId;
        $classgroup->schoolyear = INFORMAT_CURRENT_SCHOOLYEAR;
        $classgroup->code = $inschr->klasCode;
        $classgroup->name = $inschr->klas;

        $nId = $classgroupRepo->set($classgroup);
        if (!$classgroup->id) $classgroup->id = $nId;

        return $classgroup->id;
    }

    private static function CreateRegistrationClass($registrationId, $classgroupId, $inschr)
    {
        $registrationClassRepo = new InformatRegistrationClass;
        $registrationClass = $registrationClassRepo->getByInformatGuid($inschr->inschrKlasId) ?? new RegistrationClass;
        $registrationClass->informatGuid = $inschr->inschrKlasId;
        $registrationClass->informatRegistrationId = $registrationId;
        $registrationClass->informatClassGroupId = $classgroupId;
        $registrationClass->rank = $inschr->klasnummer;
        $registrationClass->start = $inschr->begindatum;
        $registrationClass->end = $inschr->einddatum;

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
