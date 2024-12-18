<?php

namespace Controllers\PAGE\INTRANET;

use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Helpers\HTML;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Repository\Sync\AD\Staff;
use Database\Repository\Informat\TeacherFreefield;
use Database\Repository\SchoolInstitute;
use Ouzo\Utilities\Clock;

class ContactStudentIdController
{
    protected $layout = [];

    public function write()
    {
        $this->getItem();
        $this->getRelations();
        $this->getAddresses();
        $this->getNumbers();
        $this->getEmails();
        $this->getBanks();
        $this->getHistory();
        return $this->layout;
    }

    private function getItem()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $item = Arrays::firstOrNull((new Student)->get($id));
        if (!$item) return;

        foreach ($item->toArray(true) as $key => $value) {
            $this->layout[$key] = [
                "pattern" => "{{student:{$key}}}",
                "content" => $value
            ];
        }
    }

    private function getRelations()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $student = Arrays::firstOrNull((new Student)->get($id));
        $items = (new StudentRelation)->getByInformatStudentId($student->id);

        $repeat = "";

        foreach ($items as $item) {
            $template = TEMPLATE_REPEAT_RELATION;
            foreach ($item->toArray(true) as $key => $value) $template = str_replace("{{relation:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["relations"] = [
            "pattern" => "{{student:repeat.relation}}",
            "content" => $repeat
        ];
    }

    private function getAddresses()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $student = Arrays::firstOrNull((new Student)->get($id));
        $items = (new StudentAddress)->getByInformatStudentId($student->id);

        $repeat = "";

        foreach ($items as $item) {
            $template = TEMPLATE_REPEAT_ADDRESS;
            foreach ($item->toArray(true) as $key => $value) $template = str_replace("{{address:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["addresses"] = [
            "pattern" => "{{student:repeat.address}}",
            "content" => $repeat
        ];
    }

    private function getNumbers()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $student = Arrays::firstOrNull((new Student)->get($id));
        $items = (new StudentNumber)->getByInformatStudentId($student->id);

        $repeat = "";

        foreach ($items as $item) {
            $template = TEMPLATE_REPEAT_NUMBER;
            foreach ($item->toArray(true) as $key => $value) $template = str_replace("{{number:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["numbers"] = [
            "pattern" => "{{student:repeat.number}}",
            "content" => $repeat
        ];
    }

    private function getEmails()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $student = Arrays::firstOrNull((new Student)->get($id));
        $items = (new StudentEmail)->getByInformatStudentId($student->id);

        $repeat = "";

        foreach ($items as $item) {
            $template = TEMPLATE_REPEAT_EMAIL;
            foreach ($item->toArray(true) as $key => $value) $template = str_replace("{{email:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["emails"] = [
            "pattern" => "{{student:repeat.email}}",
            "content" => $repeat
        ];
    }

    private function getBanks()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $student = Arrays::firstOrNull((new Student)->get($id));
        $items = (new StudentBank)->getByInformatStudentId($student->id);

        $repeat = "";

        foreach ($items as $item) {
            $template = TEMPLATE_REPEAT_BANK;
            foreach ($item->toArray(true) as $key => $value) $template = str_replace("{{bank:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["banks"] = [
            "pattern" => "{{student:repeat.bank}}",
            "content" => $repeat
        ];
    }

    private function getHistory()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $registrationRepo = new Registration;
        $registrationClassRepo = new RegistrationClass;
        $classgroupRepo = new ClassGroup;
        $instituteRepo = new SchoolInstitute;
        $schoolRepo = new School;
        $repeat = "";

        $student = Arrays::firstOrNull((new Student)->get($id));
        $registrations = $registrationRepo->getByInformatStudentId($student->id);
        $registrations = Arrays::filter($registrations, fn($cr) => $cr->status == 0);
        $registrations = array_reverse(Arrays::orderBy($registrations, "start"));

        foreach ($registrations as $registration) {
            $school = $schoolRepo->get($instituteRepo->get($registration->schoolInstituteId)[0]->schoolId)[0];
            $registration->formatted->details = $school->name . " (" . $registration->formatted->dates . ") - Stamnummer: {$registration->basenumber}";
            $registration->formatted->details .= "<ul>";

            $classRegistrations = $registrationClassRepo->getByInformatRegistrationId($registration->id);
            $classRegistrations = array_reverse(Arrays::orderBy($classRegistrations, "start"));

            foreach ($classRegistrations as $cr) {
                $classgroup = Arrays::firstOrNull($classgroupRepo->get($cr->informatClassGroupId));
                $registration->formatted->details .= "<li>{$classgroup->code} - {$classgroup->name} ({$cr->formatted->dates})</li>";
            }

            $registration->formatted->details .= "</ul>";

            $template = TEMPLATE_REPEAT_HISTORY;
            foreach ($registration->toArray(true) as $key => $value) $template = str_replace("{{history:{$key}}}", $value, $template);
            $repeat .= $template;
        }

        $this->layout["history"] = [
            "pattern" => "{{student:repeat.history}}",
            "content" => $repeat
        ];
    }
}
