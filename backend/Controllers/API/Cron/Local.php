<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User\User;
use Database\Repository\Navigation\Navigation;
use Database\Repository\User\Address;
use Database\Repository\School\School;
use Database\Repository\General\Country;
use Database\Repository\Informat\Employee;
use Database\Object\User\User as ObjectUser;
use Database\Repository\Informat\EmployeeAddress;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Object\User\Address as ObjectUserAddress;
use Database\Repository\Navigation\Setting;

abstract class Local
{
    public static function Prepare()
    {
        define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
        define("_LOGLOCATION_", "cron/local");
        Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . General::getSchoolyear());

        $informatToUser = self::InformatEmployeeToUser();
        $informatToUserAddress = self::InformatEmployeeToUserAddress();

        Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

        return ($informatToUser && $informatToUserAddress);
        // return true;
    }

    static private function InformatEmployeeToUser()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to bind Informat Employees to Users...");
        $_error_ = false;

        $employeeRepo = new Employee;
        $employeeOwnfieldRepo = new EmployeeOwnfield;
        $userRepo = new User;
        $schoolRepo = new School;

        $settingRepo = new Setting;
        $navigation = (new Navigation)->getByLink("sync");
        $_status = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.status")->value;
        $_mainSchool = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.mainSchool")->value;
        $_format = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value;
        $_firstName = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.createEmailWith")->value;

        // Temp disable users
        foreach ($userRepo->get() as $user) {
            if ($user->system) continue;

            $user->active = false;
            $userRepo->set($user);
        }

        $employees = $employeeRepo->get();
        foreach ($employees as $employee) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$employee->informatId} - {$employee->name} {$employee->firstName}");

            try {
                $firstName = (Strings::equalsIgnoreCase(($employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_firstName)->value ?? "Voornaam"), "voornaam") ? $employee->firstName : $employee->extraFirstName);
                $email = Input::createEmail($_format, $firstName, $employee->name, EMAIL_SUFFIX);

                $mainSchool = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool);
                $status = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_status);

                $user = $userRepo->getByInformatEmployeeId($employee->informatId) ?? Arrays::firstOrNull($userRepo->getByUsername($email)) ?? new ObjectUser;
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
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
                Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                $_error_ = true;
                continue;
            }
        }

        return !$_error_;
    }

    private static function InformatEmployeeToUserAddress()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Creating or updating employee addresses...");
        $_error_ = false;

        $employeeRepo = new Employee;
        $employeeAddressRepo = new EmployeeAddress;
        $userRepo = new User;
        $userAddressRepo = new Address;

        // Set all addresses as not current
        foreach ($userAddressRepo->get() as $userAddress) {
            $userAddress->current = false;
            $userAddressRepo->set($userAddress);
        }

        foreach ($employeeAddressRepo->get() as $employeeAddress) {
            $employee = $employeeRepo->getById($employeeAddress->informatEmployeeId);
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
