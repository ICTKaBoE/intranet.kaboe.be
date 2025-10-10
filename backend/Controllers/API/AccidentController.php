<?php

namespace Controllers\API;

use Helpers\ZIP;
use Helpers\Form;
use Helpers\Table;
use Security\GUID;
use Security\User;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use CloudMersive\Convert;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Accident\Party;
use Database\Repository\School\Address;
use Database\Repository\Accident\Status;
use PhpOffice\PhpWord\TemplateProcessor;
use Database\Repository\Accident\Accident;
use Database\Repository\Accident\Document;
use Database\Repository\Accident\Location;
use Database\Repository\Informat\Employee;
use Database\Repository\Navigation\Setting;
use Database\Repository\Navigation\Navigation;
use Database\Object\Accident\Accident as ObjectAccident;
use Database\Object\Accident\Document as AccidentDocument;

class AccidentController extends ApiController
{
    // Get functions
    protected function getMine($view, $id = null)
    {
        $repo = new Accident;

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

    protected function getDeclarations($view, $id = null)
    {
        $repo = new Accident;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            ];

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
        $catRepo = new Location;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $mainCategories = $catRepo->getMainCategoryOnly();
            $optgroups = $items = [];

            foreach ($mainCategories as $mainCategory) {
                $subCategories = $catRepo->getByCategoryId($mainCategory->id);

                if ($subCategories) {
                    $optgroups[] = $mainCategory;
                    foreach ($subCategories as $subCategory) {
                        $subCategory->optgroup = $mainCategory->id;
                        $subCategory->optgroupName = $mainCategory->name;
                        $subCategory->id = "{$mainCategory->id}-{$subCategory->id}";

                        $items[] = $subCategory;
                    }
                } else {
                    $mainCategory->optgroup = SELECT_OTHER_ID;
                    $items[] = $mainCategory;
                }
            }

            $optgroups[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];

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
        $this->getNavigationSettings("accident");
    }

    // Post functions
    protected function postMine($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function postDeclarations($view, $id = null)
    {
        $this->post($view, $id);
    }

    protected function post($view, $id = null)
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
            "location" => ["mandatory" => true],
            "exactLocation",
            "transport",
            "supervision" => ["convert" => "bool"],
            "informatSupervisionId" => ["type" => Input::INPUT_TYPE_INT],
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
            "witnessId" => ["default" => null, "type" => Input::INPUT_TYPE_INT]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (Arrays::first(explode("-", $fields["location"])) == "O") {
            if (!Input::check($fields["exactLocation"]) || Input::empty($fields["exactLocation"])) $this->setValidation("exactLocation", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["transport"]) || Input::empty($fields["transport"])) $this->setValidation("transport", state: self::VALIDATION_STATE_INVALID);
        }

        if ($fields["party"] == "E") {
            if (!Input::check($fields["partyExternalName"]) || Input::empty($fields["partyExternalName"])) $this->setValidation("partyExternalName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalFirstName"]) || Input::empty($fields["partyExternalFirstName"])) $this->setValidation("partyExternalFirstName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalSex"]) || Input::empty($fields["partyExternalSex"])) $this->setValidation("partyExternalSex", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalStreet"]) || Input::empty($fields["partyExternalStreet"])) $this->setValidation("partyExternalStreet", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalNumber"]) || Input::empty($fields["partyExternalNumber"])) $this->setValidation("partyExternalNumber", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalZipcode"]) || Input::empty($fields["partyExternalZipcode"])) $this->setValidation("partyExternalZipcode", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCity"]) || Input::empty($fields["partyExternalCity"])) $this->setValidation("partyExternalCity", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCountryId"]) || Input::empty($fields["partyExternalCountryId"])) $this->setValidation("partyExternalCountryId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyExternalCompany"]) || Input::empty($fields["partyExternalCompany"])) $this->setValidation("partyExternalCompany", state: self::VALIDATION_STATE_INVALID);
        } else if ($fields["party"] == "O") {
            if (!Input::check($fields["partyOtherFullName"]) || Input::empty($fields["partyOtherFullName"])) $this->setValidation("partyOtherFullName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyOtherFullAddress"]) || Input::empty($fields["partyOtherFullAddress"])) $this->setValidation("partyOtherFullAddress", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($fields["partyOtherBirthDay"]) || Input::empty($fields["partyOtherBirthDay"])) $this->setValidation("partyOtherBirthDay", state: self::VALIDATION_STATE_INVALID);
        } else if ($fields["party"] == "I") {
            if (!Input::check($fields["partyInstallReason"]) || Input::empty($fields["partyInstallReason"])) $this->setValidation("partyInstallReason", state: self::VALIDATION_STATE_INVALID);
        }

        if ($fields["police"]) {
            if (!Input::check($fields["policeName"]) || Input::empty($fields["policeName"])) $this->setValidation("policeName", state: self::VALIDATION_STATE_INVALID);
        }

        if ($fields["supervision"]) {
            if (!Input::check($fields["informatSupervisorId"], Input::INPUT_TYPE_INT) || Input::empty($fields["informatSupervisorId"])) $this->setValidation("informatSupervisorId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $accident = $repo->getById($id) ?? (new ObjectAccident);
            $accident->fillWithPostData();
            if (!$accident->creatorUserId) $accident->creatorUserId = User::getLoggedInUser()->id;
            $accident->datetime = Clock::at($fields["datetime"])->format("Y-m-d H:i:s");
            $accident->witnessId = (!is_null($fields["witnessId"]) ? $fields["witnessId"] : (User::getLoggedInUser()->informatEmployeeId ? ((new Employee)->getByInformatId(User::getLoggedInUser()->informatEmployeeId))->id : null));

            $repo->set($accident);

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
                FileSystem::CreateFolder(LOCATION_UPLOAD . "/accident");
                $file[0]->move(LOCATION_UPLOAD . "/accident/{$item->guid}.{$item->ext}");
            } else {
                $item->name = $origName;
            }

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDeclarationsPrint($view, $id = null)
    {
        if (!$id) $this->setToast("Geen aangifte geselecteerd!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $id = explode("_", $id);
            $arepo = new Accident;

            $settingsRepo = new Setting;
            $navigation = (new Navigation)->getByLink("accident");
            $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
            $filename = "Ongevallen - Aangiftes.zip";
            $templateFile = (new Document)->getById($settingsRepo->getByNavigationIdAndKey($navigation->id, "default.document")->value);

            foreach ($id as $_id) {
                $item = $arepo->getById($_id);
                $item->linked->school->linked->address = (new Address)->getBySchoolId($item->schoolId);

                $saveFilename = $folder . "/{$_id}.{$templateFile->ext}";
                $template = new TemplateProcessor(LOCATION_UPLOAD . "/accident/{$templateFile->guid}.{$templateFile->ext}");

                foreach ($item->toArray(true) as $key => $value) $template->setValue("accident:{$key}", $value);
                foreach ($item->linked->school->toArray(true) as $key => $value) $template->setValue("school:{$key}", $value);
                foreach ($item->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
                foreach ($item->linked?->supervisor?->toArray(true) ?: [] as $key => $value) $template->setValue("supervisor:{$key}", $value);
                foreach ($item->linked?->witness?->toArray(true) ?: [] as $key => $value) $template->setValue("witness:{$key}", $value);
                foreach ($item->linked?->informatStudentAddress?->toArray(true) ?: [] as $key => $value) $template->setValue("student:address.{$key}", $value);
                foreach (Arrays::flattenKeysRecursively($settingsRepo->getByNavigationId($navigation->id)) as $setting) $template->setValue("setting:{$setting->key}", $setting->value);
                foreach (User::getLoggedInUser()->toArray(true) as $key => $value) $template->setValue("user:{$key}", $value);

                $template->setValue("represent:name", $item->linked->informatStudentRelation->formatted->fullNameReversed);
                $template->setValue("represent:email", $item->linked->informatStudentEmail->email);
                $template->setValue("represent:phone", $item->linked->informatStudentNumber->number);
                $template->setValue("represent:bank.iban", $item->linked->informatStudentBank->formatted->iban);
                $template->setValue("represent:bank.bic", $item->linked->informatStudentBank->formatted->bic);

                $template->setCheckbox("checkbox:sex.m", Strings::equalsIgnoreCase($item->linked->informatStudent->sex, "M"));
                $template->setCheckbox("checkbox:sex.f", Strings::equalsIgnoreCase($item->linked->informatStudent->sex, "F"));
                $template->setCheckbox("checkbox:supervision.y", $item->supervision);
                $template->setCheckbox("checkbox:supervision.n", !$item->supervision);

                $template->setCheckbox("checkbox:location.S", Strings::startsWith($item->location, "S-"));
                $template->setCheckbox("checkbox:location.S-THEO", Strings::equalsIgnoreCase($item->location, "S-THEO"));
                $template->setCheckbox("checkbox:location.S-LO", Strings::equalsIgnoreCase($item->location, "S-LO"));
                $template->setCheckbox("checkbox:location.S-PRAC", Strings::equalsIgnoreCase($item->location, "S-PRAC"));
                $template->setCheckbox("checkbox:location.S-PLAY", Strings::equalsIgnoreCase($item->location, "S-PLAY"));
                $template->setCheckbox("checkbox:location.S-SPOR", Strings::equalsIgnoreCase($item->location, "S-SPOR"));
                $template->setCheckbox("checkbox:location.S-INT", Strings::equalsIgnoreCase($item->location, "S-INT"));
                $template->setCheckbox("checkbox:location.S-KOL", Strings::equalsIgnoreCase($item->location, "S-KOL"));

                $template->setCheckbox("checkbox:location.O", Strings::startsWith($item->location, "O-"));
                $template->setCheckbox("checkbox:location.O-TO", Strings::equalsIgnoreCase($item->location, "O-TO"));
                $template->setCheckbox("checkbox:location.O-OUT", Strings::equalsIgnoreCase($item->location, "O-OUT"));

                $template->setCheckbox("checkbox:party.O.y", Strings::equalsIgnoreCase($item->party, "O"));
                $template->setCheckbox("checkbox:party.O.n", !Strings::equalsIgnoreCase($item->party, "O"));

                $template->setCheckbox("checkbox:party.I.y", Strings::equalsIgnoreCase($item->party, "I"));
                $template->setCheckbox("checkbox:party.I.n", !Strings::equalsIgnoreCase($item->party, "I"));

                $template->setCheckbox("checkbox:party.police.y", $item->police);
                $template->setCheckbox("checkbox:party.police.n", !$item->police);

                if ($item->party == "E") {
                    $template->setValue("party:external.name", $item->partyExternalName);
                    $template->setValue("party:external.firstName", $item->partyExternalFirstName);
                    $template->setValue("party:external.speak", ($item->partyExternalSex == "M" ? "Meneer" : "Mevrouw"));
                    $template->setValue("party:external.address.street", $item->partyExternalStreet);
                    $template->setValue("party:external.address.number", $item->partyExternalNumber);
                    $template->setValue("party:external.address.bus", $item->partyExternalBus);
                    $template->setValue("party:external.address.zipcode", $item->partyExternalZipcode);
                    $template->setValue("party:external.address.city", $item->partyExternalCity);
                    $template->setValue("party:external.address.country", $item->linked->partyExternalCountry->translatedName);
                    $template->setValue("party:external.company", $item->partyExternalCompany);
                    $template->setValue("party:external.policyNumber", $item->partyExternalPolicyNumber);
                } else if ($item->party == "O") {
                    $template->setValue("party:other.fullName", $item->partyOtherFullName);
                    $template->setValue("party:other.fullAddress", $item->partyOtherFullAddress);
                    $template->setValue("party:other.birthDay", Clock::at($item->partyOtherBirthDay)->format("d/m/Y"));
                } else if ($item->party == "I") {
                    $template->setValue("party:installation", $item->partyInstallReason);
                }

                if ($item->police) {
                    $template->setValue("police:name", $item->policeName);
                    $template->setValue("police:pv", $item->policePVNumber);
                }

                $template->setValue("date:now", Clock::nowAsString("d/m/Y H:i:s"));

                foreach ($template->getVariables() as $var) $template->setValue($var, '');
                $template->saveAs($saveFilename);

                $convert = (new Convert)->convert($saveFilename, $folder . "/{$_id}.pdf");
                if ($convert) FileSystem::RemoveFile($saveFilename);

                $item->status = "C";
                $arepo->set($item);
            }

            $zip = new ZIP("{$folder}/{$filename}");
            $zip->addDir($folder);
            $zip->save();

            if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
            $this->setCloseModal();
        }
    }

    protected function postSettings()
    {
        $this->postNavigationSettings("accident");
    }

    // Delete functions

    // Mail functions
}
