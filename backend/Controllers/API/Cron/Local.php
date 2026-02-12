<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User\User;
use Database\Repository\Navigation\Navigation;
use Database\Repository\User\Address;
use Database\Repository\School\School;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\EmployeeAddress;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Object\User\Address as ObjectUserAddress;
use Database\Repository\Navigation\Setting;
use Helpers\CString;
use Helpers\General;

abstract class Local
{
    public static function Prepare()
    {
        $informatToUser = self::InformatEmployeeToUser();
        $informatToUserAddress = self::InformatEmployeeToUserAddress();

        return ($informatToUser && $informatToUserAddress);
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
        $navigation = (new Navigation)->getByLinkAndType("sync", "M");
        $_status = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.status")->value;
        $_mainSchool = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.mainSchool")->value;
        $_format = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value;
        $_firstName = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.createEmailWith")->value;

        foreach ($userRepo->get() as $user) {
            try {
                if ($user->system) continue;

                // Temp disable user
                $user->active = false;
                $userRepo->set($user);

                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "User: {$user->formatted->fullNameReversed} - " . $user->informatEmployeeId ?: "No Informat ID - SKIP");
                if (Strings::isBlank($user->informatEmployeeId)) continue;

                $employee = $employeeRepo->getByInformatId(General::removeLeadingZero(CString::getDigitsOnly($user->informatEmployeeId)));
                if (!$employee) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "Employee not found!");
                    continue;
                }

                $userMainSchool = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool)->value ?: false;
                $status = $employeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_status)->value ?: false;

                $user->mainSchoolId = $schoolRepo->getByName($userMainSchool)->id ?: $employee->linked->institute->schoolId;
                $user->bankAccount = $employee->iban;
                $user->active = $status ? Strings::equal($status, "IN DIENST") : $employee->active;
                $user->api = $status ? Strings::equal($status, "IN DIENST") : $employee->active;

                $userRepo->set($user);
            } catch (\Exception $e) {
                die(var_dump($e->getMessage()));
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
