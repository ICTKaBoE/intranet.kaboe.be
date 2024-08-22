<?php

namespace Controllers\API\Cron;

use Informat\Repository\Employee;
use Database\Repository\SchoolInstitute;
use Database\Repository\InformatEmployee;
use Database\Repository\InformatEmployeeNumber;
use Database\Repository\InformatEmployeeAddress;
use Mapper\InformatEmployeeToDatabaseInformatEmployee;
use Database\Object\InformatEmployee as ObjectInformatEmployee;
use Mapper\InformatEmployeeNumberToDatabaseInformatEmployeeNumber;
use Mapper\InformatEmployeeAddressToDatabaseInformatEmployeeAddress;
use Database\Object\InformatEmployeeNumber as ObjectInformatEmployeeNumber;
use Database\Object\InformatEmployeeAddress as ObjectInformatEmployeeAddress;
use Database\Object\InformatEmployeeOwnfield as ObjectInformatEmployeeOwnfield;
use Database\Repository\InformatEmployeeOwnfield;
use Informat\Repository\EmployeeOwnfield;
use Mapper\InformatEmployeeOwnfieldToDatabaseInformatEmployeeOwnfield;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

abstract class Informat
{
    static public function import()
    {
        $employee = self::employee();
        $students = self::students();

        return ($employee && $students);
    }

    static public function employee()
    {
        $informatEmployeeRepo = new Employee;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $dbInformatEmployeeRepo = new InformatEmployee;
        $dbInformatEmployeeAddressRepo = new InformatEmployeeAddress;
        $dbInformatEmployeeNumberRepo = new InformatEmployeeNumber;
        $dbInformatEmployeeOwnfieldRepo = new InformatEmployeeOwnfield;

        foreach ((new SchoolInstitute)->get() as $institute) {
            $employees = $informatEmployeeRepo->get($institute->numberNewFormat);
            $employeeOwnfields = $informatEmployeeOwnfieldRepo->get($institute->numberNewFormat);

            foreach ($employees as $employee) {
                $dbInformatEmployee = $dbInformatEmployeeRepo->getByInformatId($employee->pPersoon) ?? (new ObjectInformatEmployee);
                $dbInformatEmployee = (new InformatEmployeeToDatabaseInformatEmployee)->map($employee, $dbInformatEmployee);
                $newId = $dbInformatEmployeeRepo->set($dbInformatEmployee);

                if (!$dbInformatEmployee->id) $dbInformatEmployee->id = $newId;

                foreach ($employee->adressen as $address) {
                    $dbInformatEmployeeAddress = $dbInformatEmployeeAddressRepo->getByInformatId($address->id) ?? (new ObjectInformatEmployeeAddress);
                    $dbInformatEmployeeAddress->informatEmployeeId = $dbInformatEmployee->id;
                    $dbInformatEmployeeAddress = (new InformatEmployeeAddressToDatabaseInformatEmployeeAddress)->map($address, $dbInformatEmployeeAddress);
                    $dbInformatEmployeeAddressRepo->set($dbInformatEmployeeAddress);
                }

                foreach ($employee->comnrs as $number) {
                    $dbInformatEmployeeNumber = $dbInformatEmployeeNumberRepo->getByInformatId($number->id) ?? (new ObjectInformatEmployeeNumber);
                    $dbInformatEmployeeNumber->informatEmployeeId = $dbInformatEmployee->id;
                    $dbInformatEmployeeNumber = (new InformatEmployeeNumberToDatabaseInformatEmployeeNumber)->map($number, $dbInformatEmployeeNumber);
                    $dbInformatEmployeeNumberRepo->set($dbInformatEmployeeNumber);
                }

                $_employeeOwnfields = Arrays::filter($employeeOwnfields, fn ($eof) => Strings::equal($eof->personId, $employee->personId));
                foreach ($_employeeOwnfields as $eof) {
                    $dbInformatEmployeeOwnfield = $dbInformatEmployeeOwnfieldRepo->getByInformatIdAndInformatEmployeeId($eof->vvId, $dbInformatEmployee->id) ?? (new ObjectInformatEmployeeOwnfield);
                    $dbInformatEmployeeOwnfield->informatEmployeeId = $dbInformatEmployee->id;
                    $dbInformatEmployeeOwnfield = (new InformatEmployeeOwnfieldToDatabaseInformatEmployeeOwnfield)->map($eof, $dbInformatEmployeeOwnfield);
                    $dbInformatEmployeeOwnfieldRepo->set($dbInformatEmployeeOwnfield);
                }
            }
        }

        return true;
    }

    static public function students()
    {
        return true;
    }
}
