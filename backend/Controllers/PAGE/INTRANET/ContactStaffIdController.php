<?php

namespace Controllers\PAGE\INTRANET;

use Database\Repository\Informat\Employee;
use Helpers\HTML;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Repository\Sync\AD\Staff;
use Database\Repository\Informat\Teacher;
use Database\Repository\Informat\TeacherFreefield;

class ContactStaffIdController
{
    protected $layout = [];

    public function write()
    {
        $this->getItem();
        return $this->layout;
    }

    private function getItem()
    {
        $id = Helpers::getId();
        if (!$id) return;

        $item = Arrays::firstOrNull((new Employee)->get($id));
        if (!$item) return;

        // $ffRepo = new TeacherFreefield;
        // $sRepo = new School;
        // $adRepo = new Staff;

        // $freefields = $ffRepo->getByInformatTeacherIdAndSection($item->informatId, "Tewerkstelling");
        // $freefields = Arrays::filter($freefields, fn($ff) => Strings::contains($ff->description, " - Functie "));

        // $schools = Arrays::map($freefields, fn($s) => $s->description);
        // $schools = Arrays::map($schools, fn($s) => Arrays::first(explode(" - ", $s)));
        // $schools = array_unique(array_values($schools));
        // $schools = Arrays::map($schools, fn($s) => $sRepo->getByName($s));
        // $schools = Arrays::map($schools, fn($s) => $s->formatted->badge->name);

        // $functions = Arrays::map($freefields, fn($s) => $s->value);
        // $functions = array_unique(array_values($functions));
        // $functions = Arrays::map($functions, fn($f) => Arrays::first(explode(" (", $f)));

        // $ad = $adRepo->getByEmployeeId($item->informatId);

        // $item->formatted->badge->schools = implode("<br />", $schools);
        // $item->formatted->functions = implode("<br />", $functions);
        // $item->formatted->email .= ($item->formatted->email !== "" ? "<br />" : "") . HTML::Link(HTML::LINK_TYPE_EMAIL, $ad->email, $ad->email);

        foreach ($item->toArray(true) as $key => $value) {
            $this->layout[$key] = [
                "pattern" => "{{staff:{$key}}}",
                "content" => $value
            ];
        }
    }
}
