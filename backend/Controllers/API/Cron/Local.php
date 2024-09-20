<?php

namespace Controllers\API\Cron;

use Database\Object\User as ObjectUser;
use Database\Object\UserAddress as ObjectUserAddress;
use Database\Repository\School;
use Database\Repository\SchoolInstitute;
use Database\Repository\Setting;
use Database\Repository\User;
use Database\Repository\UserAddress;
use Informat\Repository\Employee;
use Informat\Repository\EmployeeOwnfield;
use Ouzo\Utilities\Strings;
use Security\Input;

abstract class Local
{
    public static function Prepare()
    {
        $informatToUser = self::InformatEmployeeToUser();

        return ($informatToUser);
    }

    private static function InformatEmployeeToUser()
    {
        $schoolRepo = new School;
        $institutes = (new SchoolInstitute)->get();
        $userRepo = new User;
        $userAddressRepo = new UserAddress;
        $informatEmployeeRepo = new Employee;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $emailFormat = (new Setting)->get("sync.email.format")[0]->value;

        foreach ($institutes as $institute) {
            $employees = $informatEmployeeRepo->get($institute->numberNewFormat);

            foreach ($employees as $employee) {
                $employeeOwnfields = $informatEmployeeOwnfieldRepo->get($institute->numberNewFormat, $employee->personId);

                $user = $userRepo->getByInformatEmployeeId($employee->pPersoon) ?? (new ObjectUser);
                $user->informatEmployeeId = $employee->pPersoon;
                $user->name = $employee->naam;
                $user->firstName = $employee->voornaam;
                $user->username = Input::createEmail($emailFormat, $user->firstName, $user->name, EMAIL_SUFFIX);
                $user->mainSchoolId = $institute->schoolId;
                $user->bankAccount = $employee->bank->iban;
                $user->active = $employee->isActive;

                foreach ($employeeOwnfields as $of) {
                    if (Strings::equal($of->naam, "pedagogische school 1")) {
                        $user->mainSchoolId = $schoolRepo->getByName($of->waarde)->id;
                        break;
                    }
                }

                $newId = $userRepo->set($user);
                if (!$user->id) $user->id = $newId;

                foreach ($employee->adressen as $address) {
                    $userAddress = $userAddressRepo->getByInformatId($address->id) ?? (new ObjectUserAddress);
                    $userAddress->userId = $user->id;
                    $userAddress->informatId = $address->id;
                    $userAddress->street = $address->straat;
                    $userAddress->number = $address->nummer;
                    $userAddress->bus = $address->bus;
                    $userAddress->zipcode = $address->postcode;
                    $userAddress->city = $address->gemeente;
                    $userAddress->country = $address->landCode;
                    $userAddress->current = $address->isDomicilie;

                    $userAddressRepo->set($userAddress);
                }
            }
        }

        return true;
    }
}
