<?php

namespace Controllers\API\Cron;

use Security\User;
use Security\Input;
use Database\Repository\Setting;
use Database\Repository\Sync\AD\Staff;
use Database\Repository\Informat\Teacher;
use Database\Object\Sync\AD\Staff as ADStaff;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\TeacherFreefield;
use Database\Repository\Navigation;
use Database\Repository\School;
use M365\Repository\User as RepositoryUser;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

abstract class Sync
{
    static public function Prepare()
    {
        $prepareEmployee = self::PrepareEmployee();
        $prepareStudent = self::PrepareStudent();

        return ($prepareEmployee && $prepareStudent);
    }

    private static function PrepareEmployee()
    {
        $informatEmployeeRepo = new Employee;
        $currentEmployees = (new RepositoryUser)->getAllEmployees(['id', 'employeeId', 'mail']);
        die(var_dump(Arrays::filter($currentEmployees, fn($ce) => Strings::contains($ce->getEmployeeId(), "12897"))));
        $informatEmployees = $informatEmployeeRepo->get();

        $currentEmployeeIds = Arrays::map($currentEmployees, fn($ce) => $ce->getEmployeeId());
        $informatEmployeeIds = Arrays::map(Arrays::filter($informatEmployees, fn($i) => $i->active), fn($ie) => $ie->informatId);
        $informatEmployeeIds[] = 123456;

        $newEmployeeIds = Arrays::filter($informatEmployeeIds, fn($iei) => !Arrays::contains($currentEmployeeIds, $iei));
        $newEmployees = Arrays::map($newEmployeeIds, fn($nei) => $informatEmployeeRepo->getByInformatId($nei));
        die(var_dump(Arrays::map($newEmployees, fn($ne) => $ne->formatted->fullName . " " . $ne->informatId)));
    }

    private static function PrepareStudent() {}
}
