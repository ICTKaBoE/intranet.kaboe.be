<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Table;
use Security\GUID;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\CString;
use Helpers\General;
use Security\FileSystem;
use CloudMersive\Convert;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\Accident\Party;
use Database\Repository\Accident\Status;
use PhpOffice\PhpWord\TemplateProcessor;
use Database\Repository\Accident\Accident;
use Database\Repository\Accident\Document;
use Database\Repository\Accident\Location;
use Database\Repository\Navigation\Setting;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentRelation;
use Database\Object\Accident\Accident as ObjectAccident;
use Database\Object\Accident\Document as AccidentDocument;
use Database\Object\Mail\Attachment as MailAttachment;
use Database\Object\Mail\Mail as MailMail;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Smartschool\Message as SmartschoolMessage;
use Database\Object\Smartschool\MessageReceiver as SmartschoolMessageReceiver;
use Database\Repository\Mail\Attachment;
use Database\Repository\Smartschool\Message;
use Database\Repository\Smartschool\MessageReceiver;
use Helpers\Filter;
use Helpers\HTML;

class AccidentController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "accident";

    // Get functions

    protected function getMine($view, $id = null)
    {
        $repo = new Accident;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId', 'creatorUserId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getDeclarations($view, $id = null)
    {
        $repo = new Accident;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getDocuments($view, $id = null)
    {
        $repo = new Document;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', $repo->get());
        else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getStatus($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Status)->get());
        }
    }

    protected function getLocation($view, $id = null)
    {
        $repo = new Location;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_items = $repo->get();

            $optgroups = array_values(Arrays::filter($_items, fn($i) => is_null($i->categoryId) && count($repo->getByCategoryId($i->id))));
            $items = array_values(Arrays::filter($_items, fn($i) => !is_null($i->categoryId) || !count($repo->getByCategoryId($i->id))));
            Arrays::each($items, fn($i) => $i->optgroup = $i->categoryId);

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', $items);
        }
    }

    protected function getParty($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Party)->get());
        }
    }

    protected function getSettings($view)
    {
        $this->getNavigationSettings();
    }

    protected function getDetailsExtranetUpdate($view, $id = null)
    {
        $repo = new Accident;
        if ($id) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getInformatStudentAddress($view, $id = null)
    {
        $repo = new StudentAddress;
        $this->appendToJson('items', Arrays::map($repo->getByInformatStudentId(Helpers::input()->get('informatStudentId')->getValue()), fn($i) => $i->toArray(true)));
    }

    protected function getInformatStudentRelation($view, $id = null)
    {
        $repo = new StudentRelation;
        $this->appendToJson('items', Arrays::map($repo->getByInformatStudentId(Helpers::input()->get('informatStudentId')->getValue()), fn($i) => $i->toArray(true)));
    }

    protected function getInformatStudentEmail($view, $id = null)
    {
        $repo = new StudentEmail;
        $this->appendToJson('items', Arrays::map($repo->getByInformatStudentId(Helpers::input()->get('informatStudentId')->getValue()), fn($i) => $i->toArray(true)));
    }

    protected function getInformatStudentNumber($view, $id = null)
    {
        $repo = new StudentNumber;
        $this->appendToJson('items', Arrays::map($repo->getByInformatStudentId(Helpers::input()->get('informatStudentId')->getValue()), fn($i) => $i->toArray(true)));
    }

    protected function getInformatStudentBank($view, $id = null)
    {
        $repo = new StudentBank;
        $this->appendToJson('items', Arrays::map($repo->getByInformatStudentId(Helpers::input()->get('informatStudentId')->getValue()), fn($i) => $i->toArray(true)));
    }

    protected function getDetails($view, $id = null)
    {
        $repo = new Accident;
        $item = $repo->getById($id);

        if (Strings::equal($view, self::VIEW_LIST)) {
            $items = [
                [
                    "title" => "Nummer",
                    "content" => $item->formatted->number
                ],
                [
                    "title" => "Status",
                    "content" => $item->linked->status->name
                ],
                [
                    "title" => "School",
                    "content" => $item->linked->school->name,
                ],
                [
                    "title" => "Klas",
                    "content" => $item->linked->informatClass->name
                ],
                [
                    "title" => "Leerling",
                    "content" => $item->linked->informatStudent->formatted->fullNameReversed
                ],
                [
                    "title" => "Beschrijving ongeval",
                    "content" => $item->visibleDescription
                ],
                [
                    "title" => "Vond plaats op",
                    "content" => $item->formatted->date . " "  . $item->formatted->time
                ]
            ];

            $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "#"));
        }
    }

    protected function getDetailsAttachments($view, $id = null)
    {
        $repo = new Accident;
        $item = $repo->getById($id);

        $files = FileSystem::PathExists(LOCATION_FILES . "/accident/{$item->guid}") ? FileSystem::getFiles(LOCATION_FILES . "/accident/{$item->guid}/*") : [];
        $b = array_values(Arrays::filter($files, fn($f) => Strings::contains($f, "/B.pdf")))[0];
        $c = array_values(Arrays::filter($files, fn($f) => Strings::contains($f, "/C.pdf")))[0];

        if (Strings::equal($view, self::VIEW_LIST)) {
            $items = [
                [
                    "title" => "Geneeskundig getuigschrift",
                    "content" => $b ? HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink($b), "Geneeskundig getuigschrift.pdf", HTML::LINK_TARGET_BLANK) : "Niet beschikbaar"
                ],
                [
                    "title" => "Informatieblad",
                    "content" => $c ? HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink($c), "Informatieblad.pdf", HTML::LINK_TARGET_BLANK) : "Niet beschikbaar"
                ],
            ];

            $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "#"));
        }
    }

    protected function getDeclarationsAttachments($view, $id = null)
    {
        $this->getDetailsAttachments($view, $id);
    }

    // Post functions
    protected function postMine($view, $id = null)
    {
        $this->postDeclarations($view, $id);
    }

    protected function postMineFast($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new Accident;

        $_fields = [
            "fast_schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "informatSubgroupId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "informatStudentId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "mail" => ["default" => false, "type" => Input::INPUT_TYPE_BOOL],
            "print" => ["default" => false, "type" => Input::INPUT_TYPE_BOOL],
            "materialDamage" => ["default" => false, "type" => Input::INPUT_TYPE_BOOL]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $address = Arrays::filter((new StudentAddress)->getByInformatStudentId($fields['informatStudentId']), fn($a) => $a->domicile);
            $bank = (new StudentBank)->getByInformatStudentId($fields['informatStudentId']);

            $accident = $repo->getById($id) ?? (new ObjectAccident);
            $accident->fillWithPostData();
            $accident->schoolId = $fields['fast_schoolId'];
            $accident->status = (new Status)->getDefault()->id;
            $accident->physicalDamage = true;
            if (count($address) == 1) $accident->informatStudentAddressId = $address[0]->id;
            if (count($bank) == 1) $accident->informatStudentBankId = $bank[0]->id;
            if (!$accident->creatorUserId) $accident->creatorUserId = User::getLoggedInUser()->id;
            $accident->datetime = Clock::at($fields["datetime"])->format("Y-m-d H:i:s");

            $nId = $repo->set($accident);
            $accident = $repo->getById($nId);

            // Create all files and Send message to creator
            $this->createDocumentsWord($nId);
            $this->sendSmartschoolMessageToCreator($nId);

            if ($fields['print']) $this->appendToJson('download', FileSystem::GetDownloadLink(LOCATION_FILES . "/accident/{$accident->guid}/{$accident->formatted->number} - B.docx"));
            $this->createCreationMail($accident->id ?? $nId);

            $this->setToast("Documenten voor dossier {$accident->formatted->number} zijn gegenereerd.");
            $this->setToast("Ouders zijn ingelicht via e-mail.");

            $this->setCloseModal();
            $this->setReloadTable();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDeclarations($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new Accident;

        $_fields = [
            "status" => ["default" => "N"],
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "informatSubgroupId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "informatStudentId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "datetime" => ["mandatory" => true],
            "description" => ["mandatory" => true],
            "visibleDescription",
            "materialDamage" => ["type" => Input::INPUT_TYPE_BOOL],
            "physicalDamage" => ["type" => Input::INPUT_TYPE_BOOL],
            "location" => ["mandatory" => true],
            "exactLocation",
            "transport",
            "supervision" => ["convert" => "bool"],
            "informatSupervisorId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "party",
            "partyExternalName",
            "partyExternalFirstName",
            "partyExternalSex",
            "partyExternalStreet",
            "partyExternalNumber",
            "partyExternalBus",
            "partyExternalZipcode",
            "partyExternalCity",
            "partyExternalCountryId" => ["type" => Input::INPUT_TYPE_INT],
            "partyExternalCompany",
            "partyExternalPolicyNumber",
            "partyOtherFullName",
            "partyOtherFullAddress",
            "partyOtherBirthDay",
            "partyInstallReason",
            "police" => ["convert" => "bool"],
            "policeName",
            "policePVNumber",
            "informatStudentRelationId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "informatStudentEmailId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "informatStudentNumberId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "informatStudentBankId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "informatStudentAddressId" => ["default" => null, "type" => Input::INPUT_TYPE_INT],
            "witness" => ["default" => false, "type" => Input::INPUT_TYPE_BOOL],
            "witnessInfo" => ["default" => null, "type" => Input::INPUT_TYPE_STRING, "mandatory" => true, "preconditions" => ["witness" => true]],
            "witnessAfter" => ["default" => false, "type" => Input::INPUT_TYPE_BOOL],
            "witnessAfterInfo" => ["default" => null, "type" => Input::INPUT_TYPE_STRING, "mandatory" => true, "preconditions" => ["witnessAfter" => true]],
            "whenAndWho" => ["default" => null, "type" => Input::INPUT_TYPE_STRING, "mandatory" => true, "preconditions" => ["witness" => false, "witnessAfter" => false]]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (Arrays::first(explode("-", $fields["location"])) == "O") {
            if (!Input::check($fields["exactLocation"]) || Input::empty($fields["exactLocation"])) $this->setValidation("exactLocation", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["transport"]) || Input::empty($fields["transport"])) $this->setValidation("transport", state: self::VALIDATION_STATE_INVALID);
        }

        $party = (new Party)->getById($fields['party'])->extendedOptions;
        if ($party == "E") {
            if (!Input::check($fields["partyExternalName"]) || Input::empty($fields["partyExternalName"])) $this->setValidation("partyExternalName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalFirstName"]) || Input::empty($fields["partyExternalFirstName"])) $this->setValidation("partyExternalFirstName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalSex"]) || Input::empty($fields["partyExternalSex"])) $this->setValidation("partyExternalSex", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalStreet"]) || Input::empty($fields["partyExternalStreet"])) $this->setValidation("partyExternalStreet", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalNumber"]) || Input::empty($fields["partyExternalNumber"])) $this->setValidation("partyExternalNumber", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalZipcode"]) || Input::empty($fields["partyExternalZipcode"])) $this->setValidation("partyExternalZipcode", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCity"]) || Input::empty($fields["partyExternalCity"])) $this->setValidation("partyExternalCity", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCountryId"]) || Input::empty($fields["partyExternalCountryId"])) $this->setValidation("partyExternalCountryId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCompany"]) || Input::empty($fields["partyExternalCompany"])) $this->setValidation("partyExternalCompany", state: self::VALIDATION_STATE_INVALID);
        } else if ($party == "O") {
            if (!Input::check($fields["partyOtherFullName"]) || Input::empty($fields["partyOtherFullName"])) $this->setValidation("partyOtherFullName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyOtherFullAddress"]) || Input::empty($fields["partyOtherFullAddress"])) $this->setValidation("partyOtherFullAddress", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyOtherBirthDay"]) || Input::empty($fields["partyOtherBirthDay"])) $this->setValidation("partyOtherBirthDay", state: self::VALIDATION_STATE_INVALID);
        } else if ($party == "I") {
            if (!Input::check($fields["partyInstallReason"]) || Input::empty($fields["partyInstallReason"])) $this->setValidation("partyInstallReason", state: self::VALIDATION_STATE_INVALID);
        }

        if ($fields["police"]) {
            if (!Input::check($fields["policeName"]) || Input::empty($fields["policeName"])) $this->setValidation("policeName", state: self::VALIDATION_STATE_INVALID);
        }

        if ($fields["supervision"]) {
            if (!Input::check($fields["informatSupervisorId"], Input::INPUT_TYPE_INT) || Input::empty($fields["informatSupervisorId"])) $this->setValidation("informatSupervisorId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $address = Arrays::filter((new StudentAddress)->getByInformatStudentId($fields['informatStudentId']), fn($a) => $a->domicile);

            $accident = $repo->getById($id) ?? (new ObjectAccident);
            $accident->fillWithPostData();
            if (!$accident->creatorUserId) $accident->creatorUserId = User::getLoggedInUser()->id;
            $accident->datetime = Clock::at($fields["datetime"])->format("Y-m-d H:i:s");
            if (count($address) == 1 && is_null($fields['informatStudentAddressId'])) $accident->informatStudentAddressId = $address[0]->id;
            if (!$fields['visibleDescription']) $accident->visibleDescription = $accident->visibleDescription ?? $fields['description'];

            $nId = $repo->set($accident);

            // Create all files
            $this->createDocumentsWord($accident->id ?? $nId);
            if (!$accident->id) $this->createCreationMail($nId);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDocuments($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "alias" => ["mandatory" => true],
            "file" => ["type" => "file"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Document;
            $file = $fields["file"];

            $item = $repo->getById($id) ?? new AccidentDocument;
            $origName = $item->name;
            $item->fillWithPostData();
            $item->guid = $item->guid ?? GUID::create();

            if ($file && $file[0]->getSize() > 0) {
                $item->name = $file[0]->getFilename();
                $item->ext = $file[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/accident/_default_/");
                $file[0]->move(LOCATION_FILES . "/accident/_default_/{$item->guid}.{$item->ext}");
            } else {
                $item->name = $origName;
            }

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function printDeclarationsPrint($view, $id = null)
    {
        if (!$id) $this->setToast("Geen aangifte geselecteerd!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings()
    {
        $this->postNavigationSettings();
    }

    protected function postExtranetRequest($view, $id = null)
    {
        $_fields = [
            "filenumber" => ["mandatory" => true, "placeholder" => "____-______"],
            "insz" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Accident;
            $id = CString::removeLeadingZeros(explode("-", $fields["filenumber"])[1]);

            $item = $repo->getById($id);
            if ($item && Strings::equal(CString::getDigitsOnly($item?->linked->informatStudent->insz), CString::getDigitsOnly($fields['insz']))) $this->setRedirect("/details/{$item->guid}");
            else $this->setToast("Dossier niet gevonden!<br />Gelieve de juiste waarden in te vullen!", self::VALIDATION_STATE_INVALID);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDetailsExtranetUpdate($view, $id = null)
    {
        $_fields = [
            "informatStudentId",
            "informatStudentAddressId" => ["mandatory" => true],
            "informatStudentRelationId" => ["mandatory" => true],
            "informatStudentEmailId" => ["mandatory" => true],
            "informatStudentNumberId" => ["mandatory" => true],
            "informatStudentBankId" => ["mandatory" => true],
            "documentB" => ["type" => "file"],
            "documentC" => ["type" => "file"],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Accident;

            $item = $repo->getById($id);
            $item->fillWithPostData();
            $repo->set($item);

            $documentB = $fields["documentB"];
            if ($documentB && $documentB[0]->getSize() > 0) {
                $ext = $documentB[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/accident/{$item->guid}");
                $documentB[0]->move(LOCATION_FILES . "/accident/{$item->guid}/B.{$ext}");
            }

            $documentC = $fields["documentC"];
            if ($documentC && $documentC[0]->getSize() > 0) {
                $ext = $documentC[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/accident/{$item->guid}");
                $documentC[0]->move(LOCATION_FILES . "/accident/{$item->guid}/C.{$ext}");
            }

            // $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDeclarationsMail($view, $id = null)
    {
        $accidentRepo = new Accident;
        $settingsRepo = new Setting;

        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $mailAttachmentRepo = new Attachment;

        foreach (explode("_", $id) as $_id) {
            $accident = $accidentRepo->getById($_id);

            $mail = new MailMail;

            $fromEmail = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.email")->value;
            $fromName = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.name")->value;
            $toEmail = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "insurance.email")->value;
            $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.insurance.subject")->value;
            $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.insurance.body")->value;

            foreach ($accident->toArray(true) as $key => $value) {
                $subject = str_replace("{{{$key}}}", $value, $subject);
                $body = str_replace("{{{$key}}}", $value, $body);
            }

            $mail->fromEmail = $fromEmail;
            $mail->fromName = $fromName;
            $mail->subject = $subject;
            $mail->body = $body;

            $mId = $mailRepo->set($mail);

            $files = [
                "{$accident->formatted->number} - Aangifte Fiche (A).pdf" => LOCATION_FILES . "/accident/{$accident->guid}/{$accident->formatted->number} - A.docx",
                "{$accident->formatted->number} - Informatieblad (C).pdf" => LOCATION_FILES . "/accident/{$accident->guid}/C.pdf",
            ];

            if ($accident->physicalDamage) $files[] = [
                "{$accident->formatted->number} - Geneeskundig getuigschrift (B).pdf" => LOCATION_FILES . "/accident/{$accident->guid}/B.pdf",
            ];

            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->email = $toEmail;
            $mailReceiverRepo->set($receiver);

            foreach ($files as $name => $path) {
                if (!Strings::endsWith($path, ".pdf")) (new Convert)->convert($path, str_replace(".docx", ".pdf", $path));

                $attachment = new MailAttachment;
                $attachment->mailId = $mId;
                $attachment->path = str_replace(".docx", ".pdf", $path);
                $attachment->name = $name;
                $mailAttachmentRepo->set($attachment);
            }

            $accident->status = (new Status)->getWhenInsuranceIsMailed()->id;
            $accidentRepo->set($accident);

            $this->setToast("Dossier {$accident->formatted->number} is verstuurd naar de verzekering");
        }

        $this->setReloadTable();
    }

    // Delete functions

    // Mail functions
    private function sendSmartschoolMessageToCreator($accidentId)
    {
        $settingsRepo = new Setting;
        $smsMessageRepo = new Message;
        $smsMessageReceiverRepo = new MessageReceiver;
        $accident = (new Accident)->getById($accidentId);

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.creator.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.creator.body")->value;

        foreach ($accident->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $message = new SmartschoolMessage;
        $message->sourceId = $$accident->linked->school->smartschoolSourceId ?? $accident->linked->school->linked->parentSchool->smartschoolSourceId;
        $message->subject = Strings::trimToNull($subject);
        $message->body = Strings::trimToNull($body);

        $messageId = $smsMessageRepo->set($message);

        $receiver = new SmartschoolMessageReceiver;
        $receiver->messageId = $messageId;
        $receiver->username = DEV_MODE ? DEV_CONTACT : $accident->linked->creatorUser->username;

        $smsMessageReceiverRepo->set($receiver);
    }

    private function createCreationMail($accidentId, $attachments = [])
    {
        $settingsRepo = new Setting;
        $accident = (new Accident)->getById($accidentId);

        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $mailAttachmentRepo = new Attachment;

        $mail = new MailMail;

        $fromEmail = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.email")->value;
        $fromName = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.name")->value;
        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.parent.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.parent.body")->value;

        foreach ($accident->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        foreach ($accident->linked->informatStudent->toArray(true) as $key => $value) {
            $subject = str_replace("{{student:{$key}}}", $value, $subject);
            $body = str_replace("{{student:{$key}}}", $value, $body);
        }

        $mail->fromEmail = $fromEmail;
        $mail->fromName = $fromName;
        $mail->subject = $subject;
        $mail->body = $body;

        $mId = $mailRepo->set($mail);

        foreach ((new StudentEmail)->getByInformatStudentId($accident->informatStudentId) as $email) {
            if (!Arrays::contains(["moeder", "vader", "plusvader", "plusmoeder", "pleegmoeder", "pleegvader", "meemoeder", "meevader", "ouders"], strtolower($email->type))) continue;

            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->email = DEV_MODE ? DEV_CONTACT : $email->email;
            $receiver->name = "{$email->type} van {$accident->linked->informatStudent->formatted->fullNameReversed}";
            $mailReceiverRepo->set($receiver);
        }

        foreach (["Geneeskundig getuigschrift" => "B", "Informatieblad" => "C"] as $name => $part) {
            (new Convert)->convert(LOCATION_FILES . "/accident/{$accident->guid}/{$accident->formatted->number} - {$part}.docx", LOCATION_FILES . "/accident/{$accident->guid}/{$accident->formatted->number} - {$part}.pdf");

            $attachment = new MailAttachment;
            $attachment->mailId = $mId;
            $attachment->path = LOCATION_FILES . "/accident/{$accident->guid}/{$accident->formatted->number} - {$part}.pdf";
            $attachment->name = "{$accident->formatted->number} - {$name} ({$part}).pdf";
            $mailAttachmentRepo->set($attachment);
        }
    }

    // Other functions
    private function createDocumentsWord($accidentId)
    {
        $item = (new Accident)->getById($accidentId);
        $settingsRepo = new Setting;
        $documentRepo = new Document;
        $locations = (new Location)->get();
        $parties = (new Party)->get();

        // Create file
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/accident/{$item->guid}");

        $templateFiles = [
            "A" => $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.a")->value),
            "B" => $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.b")->value),
            "C" => $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.c")->value)
        ];

        foreach ($templateFiles as $part => $templateFile) {
            $saveFilename = $folder . "/{$item->formatted->number} - {$part}.{$templateFile->ext}";
            $template = new TemplateProcessor(LOCATION_FILES . "/accident/_default_/{$templateFile->guid}.{$templateFile->ext}");

            foreach ($item->toArray(true) as $key => $value) $template->setValue("accident:{$key}", $value);
            foreach ($item->linked->school->toArray(true) as $key => $value) $template->setValue("school:{$key}", $value);
            foreach ($item->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
            foreach ($item->linked?->supervisor?->toArray(true) ?: [] as $key => $value) $template->setValue("supervisor:{$key}", $value);
            foreach ($item->linked?->witness?->toArray(true) ?: [] as $key => $value) $template->setValue("witness:{$key}", $value);
            foreach ($item->linked?->informatStudentAddress?->toArray(true) ?: [] as $key => $value) $template->setValue("student:address.{$key}", $value);
            foreach (Arrays::flattenKeysRecursively($settingsRepo->getByNavigationId(CURRENT_NAVIGATION_MODULE_ID)) as $setting) $template->setValue("setting:{$setting->key}", $setting->value);
            foreach (User::getLoggedInUser()->toArray(true) as $key => $value) $template->setValue("user:{$key}", $value);
            foreach ($item->linked?->informatStudentRelation?->toArray(true) ?: [] as $key => $value) $template->setValue("represent:{$key}", $value);
            foreach ($item->linked?->informatStudentEmail?->toArray(true) ?: [] as $key => $value) $template->setValue("represent:{$key}", $value);
            foreach ($item->linked?->informatStudentNumber?->toArray(true) ?: [] as $key => $value) $template->setValue("represent:{$key}", $value);
            foreach ($item->linked?->informatStudentBank?->toArray(true) ?: [] as $key => $value) $template->setValue("represent:{$key}", $value);

            $template->setValue("supervision:y", ($item->supervision ? 'X' : ''));
            $template->setValue("supervision:n", (!$item->supervision ? 'X' : ''));

            $template->setValue("witness:y", ($item->witness ? 'X' : ''));
            $template->setValue("witness:n", (!$item->witness ? 'X' : ''));

            $template->setValue("witnessAfter:y", ($item->witnessAfter ? 'X' : ''));
            $template->setValue("witnessAfter:n", (!$item->witnessAfter ? 'X' : ''));

            foreach ($locations as $location) {
                if (!$location->categoryId) continue;
                $template->setValue("location:{$location->categoryId}-{$location->id}", (Strings::equalsIgnoreCase($item->location, "{$location->categoryId}-{$location->id}") ? "X" : ""));
            }

            foreach ($parties as $party) {
                $template->setValue("party:{$party->extendedOptions}.y", (Strings::equalsIgnoreCase($item->party, $party->id) ? 'X' : ''));
                $template->setValue("party:{$party->extendedOptions}.n", (!Strings::equalsIgnoreCase($item->party, $party->id) ? 'X' : ''));
            }

            $template->setValue("police:y", ($item->police ? 'X' : ''));
            $template->setValue("police:n", (!$item->police ? 'X' : ''));

            $extraInfo = "";

            $party = (new Party)->getById($item->party)->extendedOptions;
            if ($party == "E") {
                $eAddress = CString::formatAddress($item->partyExternalStreet, $item->partyExternalNumber, $item->partyExternalBus, $item->partyExternalZipcode, $item->partyExternalCity, $item->linked->partyExternalCountry->translatedName);
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "1:  " . ($item->partyExternalSex == "M" ? "Meneer" : "Mevrouw") . " {$item->partyExternalName} {$item->partyExternalFirstName}\n{$eAddress}\n{$item->partyExternalCompany} - {$item->partyExternalPolicyNumber}";
            } else if ($party == "O") {
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "2:  {$item->partyOtherFullName}\n{$item->partyOtherFullAddress}\n" . Clock::at($item->partyOtherBirthDay)->format("d/m/Y");
            } else if ($party == "I") {
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "3:  {$item->partyInstallReason}";
            }

            if ($item->police) {
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "4:  {$item->policeName} - {$item->policePVNumber}";
            }

            if ($item->witness) {
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "5:  {$item->witnessInfo}";
            }

            if ($item->witnessAfter) {
                if (Strings::isNotBlank($extraInfo)) $extraInfo .= "\n";
                $extraInfo .= "6:  {$item->witnessAfterInfo}";
            }

            $extraInfo = ltrim($extraInfo);
            $extraInfo = rtrim($extraInfo);
            $template->setValue("extraInfo", $extraInfo);
            $template->setValue("date:now", Clock::nowAsString("d/m/Y H:i:s"));

            foreach ($template->getVariables() as $var) $template->setValue($var, '');
            $template->saveAs($saveFilename);
        }
    }
}
