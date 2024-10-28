<?php

namespace Controllers\API\Cron;

use Database\Object\User as ObjectUser;
use Database\Repository\Informat\Teacher;
use Database\Repository\Informat\TeacherFreefield;
use Database\Repository\School;
use Database\Repository\Setting;
use Database\Repository\User;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Input;

abstract class Local
{
    public static function Prepare()
    {
        $informatToUser = self::InformatTeacherToUser();

        return ($informatToUser);
    }

    private static function InformatTeacherToUser()
    {
        $informatTeacherRepo = new Teacher;
        $informatTeacherFreefields = new TeacherFreefield;
        $userRepo = new User;
        $settingRepo = new Setting;
        $schools = (new School)->get();
        $mainSchoolField = $settingRepo->get("informat.ownfieldname.mainSchool")[0]->value;
        $statusField = $settingRepo->get("informat.ownfieldname.status")[0]->value;
        $emailFormat = $settingRepo->get("sync.email.format")[0]->value;

        $teachers = $informatTeacherRepo->get();

        foreach ($teachers as $teacher) {
            $user = $userRepo->getByInformatId($teacher->informatId) ?? new ObjectUser;
            if (!$user->id && !$teacher->active) continue;

            $mainSchool = $informatTeacherFreefields->getByInformatTeacherIdSesionAndDescription($teacher->informatId, "Tewerkstelling", $mainSchoolField);
            $status = $informatTeacherFreefields->getByInformatTeacherIdSesionAndDescription($teacher->informatId, "Tewerkstelling", $statusField);

            $user->informatId = $teacher->informatId;
            $user->mainSchoolId = Arrays::firstOrNull(Arrays::filter($schools, fn($s) => Strings::equal($s->name, $mainSchool->value)))->id;
            $user->username = Input::createEmail($emailFormat, $teacher->firstName, $teacher->name, EMAIL_SUFFIX);
            $user->name = $teacher->name;
            $user->firstName = $teacher->firstName;
            $user->bankAccount = $teacher->bankAccount;
            $user->active = $teacher->active;
            if ($status) $user->active = Strings::equal($status->value, "IN DIENST");

            $userRepo->set($user);
        }

        return true;
    }
}
