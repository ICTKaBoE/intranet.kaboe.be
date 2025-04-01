<?php

namespace Controllers\API\Cron;

use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User\User;
use Database\Repository\School\School;
use Database\Repository\Country;
use Database\Object\User\User as ObjectUser;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\EmployeeAddress;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Object\User\Address as ObjectUserAddress;
use Database\Repository\Navigation;
use Database\Repository\User\Address;

abstract class Local
{
    public static function Prepare()
    {
        $informatToUser = self::InformatEmployeeToUser();
        $informatToUserAddress = self::InformatEmployeeToUserAddress();

        return ($informatToUser && $informatToUserAddress);
        // return true;
    }

    static private function InformatEmployeeToUser()
    {
        $_error_ = false;

        $employeeRepo = new Employee;
        $employeeOwnfieldRepo = new EmployeeOwnfield;
        $userRepo = new User;
        $schoolRepo = new School;

        $_settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, 'sync'))->settings;
        $_status = $_settings['informat']['ownfield']['status'];
        $_mainSchool = $_settings['informat']['ownfield']['mainSchool'];
        $_format = $_settings['format']['email'];
        $_firstName = $_settings['informat']['ownfield']['createEmailWith'];

        // Temp disable users
        foreach ($userRepo->get() as $user) {
            if ($user->system) continue;

            $user->active = false;
            $userRepo->set($user);
        }

        $employees = $employeeRepo->get();
        foreach ($employees as $employee) {
            try {
                $firstName = (Strings::equalsIgnoreCase(($employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_firstName)->value ?: "Officiële voornaam"), "officiële voornaam") ? $employee->firstName : $employee->extraFirstName);
                $email = Input::createEmail($_format, $firstName, $employee->name, EMAIL_SUFFIX);
                $user = $userRepo->getByInformatEmployeeId($employee->informatId) ?? Arrays::firstOrNull($userRepo->getByUsername($email)) ?? new ObjectUser;

                $mainSchool = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool);
                $status = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_status);

                $user->informatEmployeeId = $employee->informatId;
                $user->mainSchoolId = $schoolRepo->getByName($mainSchool->value)->id ?: 0;
                $user->username = $email;
                $user->name = $employee->name;
                $user->firstName = $firstName;
                $user->bankAccount = $employee->iban;
                $user->active = $employee->active;
                $user->api = $user->active;

                if ($status) {
                    $user->active = Strings::equal($status->value, "IN DIENST");
                    $user->api = Strings::equal($status->value, "IN DIENST");
                }

                $userRepo->set($user);
            } catch (\Exception $e) {
                $_error_ = true;
                continue;
            }
        }

        return !$_error_;
    }

    private static function InformatEmployeeToUserAddress()
    {
        $_error_ = false;

        $employeeRepo = new Employee;
        $employeeAddressRepo = new EmployeeAddress;
        $userRepo = new User;
        $userAddressRepo = new Address;
        $countryRepo = new Country;

        // Set all addresses as not current
        foreach ($userAddressRepo->get() as $userAddress) {
            $userAddress->current = false;
            $userAddressRepo->set($userAddress);
        }

        foreach ($employeeAddressRepo->get() as $employeeAddress) {
            $employee = Arrays::firstOrNull($employeeRepo->get($employeeAddress->informatEmployeeId));
            if (!$employee) continue;

            $user = $userRepo->getByInformatEmployeeId($employee->informatId);
            if (!$user) continue;

            $address = $userAddressRepo->getByInformatEmployeeAddressId($employeeAddress->id) ?? new ObjectUserAddress;
            $address->userId = $user->id;
            $address->informatEmployeeAddressId = $employeeAddress->id;
            $address->street = $employeeAddress->street;
            $address->number = $employeeAddress->number;
            $address->bus = $employeeAddress->bus;
            $address->zipcode = $employeeAddress->zipcode;
            $address->city = $employeeAddress->city;
            $address->countryId = $employeeAddress->countryId;
            $address->current = $employeeAddress->current;

            $userAddressRepo->set($address);
        }

        return !$_error_;
    }
}
