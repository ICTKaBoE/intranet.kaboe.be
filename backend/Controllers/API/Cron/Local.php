<?php

namespace Controllers\API\Cron;

use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User;
use Database\Repository\School;
use Database\Repository\Country;
use Database\Repository\Setting;
use Database\Repository\UserAddress;
use Database\Object\User as ObjectUser;
use Database\Repository\Informat\Teacher;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\EmployeeAddress;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\TeacherFreefield;
use Database\Object\UserAddress as ObjectUserAddress;

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
        $settingRepo = new Setting;

        $_mainSchool = Arrays::first($settingRepo->get("informat.ownfieldname.mainSchool"))->value;
        $_status = Arrays::first($settingRepo->get("informat.ownfieldname.status"))->value;
        $_format = Arrays::first($settingRepo->get("sync.format.email"))->value;

        // Temp disable users
        foreach ($userRepo->get() as $user) {
            if ($user->system) continue;

            $user->active = false;
            $userRepo->set($user);
        }

        $employees = $employeeRepo->get();
        foreach ($employees as $employee) {
            try {
                $user = $userRepo->getByInformatEmployeeId($employee->informatId) ?? new ObjectUser;

                $mainSchool = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool);
                $status = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_status);

                $user->informatEmployeeId = $employee->informatId;
                $user->mainSchoolId = $schoolRepo->getByName($mainSchool->value)->id;
                $user->username = Input::createEmail($_format, $employee->firstName, $employee->name, EMAIL_SUFFIX);
                $user->name = $employee->name;
                $user->firstName = $employee->firstName;
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
        $userAddressRepo = new UserAddress;
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
            $address->countryId = $countryRepo->getByNisCode(General::removeLeadingZero($employeeAddress->countryCode))->id;
            $address->current = $employeeAddress->current;

            $userAddressRepo->set($address);
        }

        return !$_error_;
    }
}
