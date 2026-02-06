<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\Export\Export as ExportExport;
use Database\Repository\Mail\Mail;
use Database\Repository\Violence\Out;
use Database\Repository\Export\Export;
use Database\Repository\Mail\Receiver;
use Database\Repository\School\School;
use Database\Repository\Violence\Cause;
use Database\Repository\Violence\Damage;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Violence\Violence;
use Database\Repository\Navigation\Setting;
use Database\Repository\Security\GroupUser;
use Database\Repository\Violence\Intention;
use Database\Repository\Violence\DamageKind;
use Database\Repository\Violence\Consequence;
use Database\Repository\Navigation\Navigation;
use Database\Repository\User\User as UserUser;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Violence\Form as ViolenceForm;
use Database\Object\Violence\Violence as ViolenceViolence;

class ViolenceController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "violence";

    const FIELDS = [
        1 => [
            "schoolId" => ["mandatory" => true, "convert" => Input::INPUT_TYPE_INT],
            "anonymous" => ["convert" => Input::INPUT_TYPE_BOOL],
            "victimId" => ["convert" => Input::INPUT_TYPE_INT, "mandatory" => true, "preconditions" => ["anonymous" => false]]
        ],
        2 => [
            "factsDate" => ["mandatory" => true],
            "identityParty",
            "ageParty",
            "workingHours" => ["mandatory" => true, "convert" => Input::INPUT_TYPE_BOOL]
        ],
        3 => [
            "form" => ["mandatory" => true],
            "formOther",
            "out" => ["mandatory" => true],
            "outOther",
            "intention" => ["mandatory" => true],
            "intentionOther",
            "consequence"
        ],
        4 => [
            "cause",
            "causeOther"
        ],
        5 => [
            "damage",
            "damageKind",
            "police" => ["mandatory" => true]
        ],
        6 => [
            "actionsTaken",
            "proposalEmployer",
            "proposalConfidant",
            "proposalPapsy",
            "proposalHead"
        ]
    ];

    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new Violence;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
                'creatorUserId' => Arrays::filter(explode(";", Helpers::url()->getParam("creatorUserId")), fn($i) => Strings::isNotBlank($i)),
            ];

            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAll($view, $id = null)
    {
        $repo = new Violence;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            ];

            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getForm($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new ViolenceForm)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
        }
    }

    protected function getOut($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Out)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
        }
    }

    protected function getIntention($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Intention)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
        }
    }

    protected function getConsequence($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Consequence)->get());
        }
    }

    protected function getCause($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = (new Cause)->get();
            $items[] = SELECT_OTHER;
            $this->appendToJson('items', $items);
        }
    }

    protected function getDamage($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Damage)->get());
        }
    }

    protected function getDamageKind($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new DamageKind)->get());
        }
    }

    protected function getSettings($view)
    {
        $this->getNavigationSettings();
    }

    // Post functions 
    protected function postMine($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function postAll($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function post($view, $id = null)
    {
        $_steps = 6;

        if ($id == "add") $id = null;

        // Step Check
        if (Helpers::input()->exists("_step_")) {
            $_step = (int)Helpers::input()->post("_step_")->getValue();
            $_stepDirection = Helpers::input()->post("_stepDirection_")->getValue();

            if ($_stepDirection == "+") {
                [$invalid, $fields] = Form::Validate(self::FIELDS[$_step]);
                Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue(self::FIELDS, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));
            }

            if ($this->validationIsAllGood()) {
                $_nextStep = $_stepDirection == "+" ? $_step + 1 : $_step - 1;
                if ($_nextStep < 1) $_nextStep = 1;
                if ($_nextStep > $_steps) $_nextStep = $_steps;

                $this->setActiveStep($_nextStep);
                if ($_nextStep == $_steps) {
                    $this->setActiveButton("Submit");
                    $this->setActiveButton("PrevStep");
                    $this->setNotActiveButton("NextStep");
                } else if ($_nextStep == $_steps - 1) {
                    $this->setActiveButton("PrevStep");
                    $this->setActiveButton("NextStep");
                    $this->setNotActiveButton("Submit");
                } else if ($_nextStep == 1) {
                    $this->setActiveButton("NextStep");
                    $this->setNotActiveButton("Submit");
                    $this->setNotActiveButton("PrevStep");
                } else {
                    $this->setActiveButton("NextStep");
                    $this->setActiveButton("PrevStep");
                    $this->setNotActiveButton("Submit");
                }
            } else $this->setToast("Gelieve de vereiste velden in te vullen of de fouten te corrigeren!", self::VALIDATION_STATE_INVALID);
        }
        // Global checks
        else {
            $repo = new Violence;

            $item = $repo->getById($id) ?? new ViolenceViolence;
            $item->fillWithPostData();
            $item->creatorUserId = User::getLoggedInUser()->id;

            $nId = $repo->set($item);
            $this->mailNew($nId);

            if ($this->validationIsAllGood()) $this->setReturn();
        }
    }

    protected function postSettings()
    {
        $this->postNavigationSettings();
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "school" => ["mandatory" => true],
            "start" => ["default" => null],
            "end" => ["default" => null]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (is_array($fields['school']) && !Strings::contains($fields['school'], ";")) {
            $s = [];
            foreach ($fields['school'] as $sch)
                $s[] = $sch->getValue();

            $fields['school'] = $s;
        } else if (Strings::contains($fields['school'], ";")) {
            $fields['school'] = explode(";", $fields['school']);
        } else $fields['school'] = [$fields['school']];

        if ($this->validationIsAllGood()) {
            if ($fields['start']) $fields['start'] .= " 00:00:00";
            if ($fields['end']) $fields['end'] .= " 23:59:59";

            $this->export($fields['school'], $fields['start'], $fields['end']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Mail functions
    protected function mailNew($id)
    {
        $repo = new Violence;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.new.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.new.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.new.reply")->value, "bool")) $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        foreach (explode(";", $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.to.groupIds")->value) as $groupId) {
            $members = (new GroupUser)->getBySecurityGroupId($groupId);

            foreach ($members as $member) {
                $user = (new UserUser)->getById($member->userId);
                $receiver = new MailReceiver;
                $receiver->mailId = $mId;
                $receiver->email = $user->username;
                $receiver->name = $user->fullName;
                $mailReceiverRepo->set($receiver);
            }
        }
    }

    // Export functions
    private function export($schoolIds, $start, $end)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/violence/" . date("YmdHis"));
        $filename = "Melding Van Geweld.xlsx";

        $items = $this->getAllGroupedBySchool($schoolIds, $start, $end);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($schoolIds as $index => $schoolId) {
            $school = $schoolRepo->getById($schoolId);

            if ($index == 0) $excel->setSheetTitle($index, $school->name);
            else $excel->createSheet($index, $school->name);

            $excel->setCellValue($index, "A1:P1", "Melding van geweld - {$school->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", is_null(Strings::trimToNull($start)) ? "" : Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", is_null(Strings::trimToNull($end)) ? "" : Clock::at($end)->format("d/m/Y"));

            $table = [];
            $table["header"] = [
                "Aangegeven op",
                "Slachtoffer",
                "Datum van de feiten",
                "Derde",
                "Tijdens de werkuren",
                "Vorm",
                "Uiting",
                "Intentie van de derde",
                "Gevolgen",
                "Aanleiding",
                "Schade of gevolg",
                "Soort schade of gevolg",
                "Aangifte bij politie",
                "Reeds genomen acties",
                "Voorstellen aan werkgever",
                "Voorstellen aan vertrouwenspersoon",
                "Voorstellen voor papsy",
                "Voorstellen voor leidinggevende"
            ];

            foreach ($items[$schoolId] as $i => $item) {
                $table["data"][$i] = [
                    $item->formatted->creationDateTime->display,
                    $item->formatted->victim,
                    $item->formatted->factsDate->display,
                    $item->formatted->party,
                    $item->formatted->workingHours,
                    $item->formatted->form,
                    $item->formatted->out,
                    $item->formatted->intention,
                    $item->linked->consequence->name,
                    $item->formatted->cause,
                    $item->formatted->damage,
                    $item->formatted->damageKind,
                    $item->formatted->police,
                    $item->actionsTaken,
                    $item->proposalEmployer,
                    $item->proposalConfidant,
                    $item->proposalPapsy,
                    $item->proposalHead
                ];
            }

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getAllGroupedBySchool($schoolIds, $start, $end)
    {
        $return = [];
        $repo = new Violence;

        foreach ($schoolIds as $schoolId) {
            $return[$schoolId] = $repo->getBySchoolId($schoolId);
            if ($start) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->factsDateTime)->isAfterOrEqualTo(Clock::at($start)));
            if ($end) $return[$schoolId] = Arrays::filter($return[$schoolId], fn($i) => Clock::at($i->factsDateTime)->isBeforeOrEqualTo(Clock::at($end)));
        }

        return $return;
    }
}
