<?php

namespace Controllers\API\Cron;

use Database\Object\Informat\Teacher as InformatTeacher;
use Database\Object\Informat\TeacherFreefield as InformatTeacherFreefield;
use Database\Repository\Informat\Teacher;
use Database\Repository\Informat\TeacherFreefield;
use Database\Repository\SchoolInstitute;
use Informat\SOAP\Repository\Leerkrachten;
use Informat\SOAP\Repository\LeerkrachtenVrijevelden;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

abstract class Informat
{
    static public function Import()
    {
        $employee = self::Teacher();
        $students = self::Students();

        return ($employee && $students);
    }

    static public function Teacher()
    {
        $informatLeerkrachtenRepo = new Leerkrachten;
        $informatLeerkrachtVrijeveldenRepo = new LeerkrachtenVrijevelden;
        $schoolInstitutes = (new SchoolInstitute)->get();

        foreach ($schoolInstitutes as $institute) {
            $informatLeerkrachtenRepo->setInstituteNumber($institute->numberNewFormat);
            $informatLeerkrachtVrijeveldenRepo->setInstituteNumber($institute->numberNewFormat);

            $leerkrachten = $informatLeerkrachtenRepo->get();
            $vrijeVelden = $informatLeerkrachtVrijeveldenRepo->get();

            $teacherRepo = new Teacher;
            foreach ($leerkrachten as $leerkracht) {
                $teacher = $teacherRepo->getByInformatId($leerkracht->p_persoon) ?? new InformatTeacher;
                $teacher->informatId = $leerkracht->p_persoon;
                $teacher->basenumber = $leerkracht->Stamnummer;
                $teacher->name = $leerkracht->Naam;
                $teacher->firstName = $leerkracht->Voornaam;
                $teacher->schoolyear = $leerkracht->Schooljaar;
                $teacher->homePhone = $leerkracht->Thuistelefoon;
                $teacher->mobilePhone = $leerkracht->Gsm;
                $teacher->email = $leerkracht->Prive_email;
                $teacher->street = $leerkracht->Straat;
                $teacher->number = $leerkracht->Nr;
                $teacher->bus = $leerkracht->Bus;
                $teacher->zipcode = $leerkracht->Dlpostnr;
                $teacher->city = $leerkracht->Dlgem;
                $teacher->countryCode = $leerkracht->Landcode;
                $teacher->active = Strings::equal($leerkracht->Actief, "J");
                $teacher->bankAccount = $leerkracht->Iban;

                $teacherRepo->set($teacher);
            }

            $teacherFreefieldRepo = new TeacherFreefield;
            foreach ($vrijeVelden as $vrijveld) {
                $freefield = $teacherFreefieldRepo->getByInformatTeacherIdSesionAndDescription($vrijveld->pPersoon, $vrijveld->Rubriek, $vrijveld->OmschrijvingVrijVeld) ?? new InformatTeacherFreefield;
                $freefield->informatTeacherId = $vrijveld->pPersoon;
                $freefield->description = $vrijveld->OmschrijvingVrijVeld;
                $freefield->value = $vrijveld->WaardeVrijVeld;
                $freefield->section = $vrijveld->Rubriek;

                $teacherFreefieldRepo->set($freefield);
            }
        }

        return true;
    }

    static public function Students()
    {
        return true;
    }
}
