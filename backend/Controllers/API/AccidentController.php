<?php

namespace Controllers\API;

use Helpers\ZIP;
use Security\GUID;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Security\FileSystem;
use CloudMersive\Convert;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\Accident as ObjectAccident;
use Database\Repository\Accident;
use Database\Repository\Informat\Employee;
use Database\Repository\Navigation;
use Database\Repository\SchoolAddress;
use PhpOffice\PhpWord\TemplateProcessor;

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

            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[1, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "#",
                        "data" => "formatted.number",
                        "width" => "120px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Klas",
                        "data" => "linked.informatClass.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Getroffen leerling",
                        "data" => "linked.informatStudent.formatted.fullNameReversed"
                    ],
                    [
                        "title" => "Locatie",
                        "data" => "formatted.location",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Ongeval te wijten aan...",
                        "data" => "formatted.party",
                        "width" => "250px"
                    ],
                    [
                        "title" => "Aangegeven op",
                        "data" => "formatted.creationDateTime",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                ]
            );

            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->get($id)[0]);
    }

    protected function getDeclarations($view, $id = null)
    {
        $repo = new Accident;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[2, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "title" => "#",
                        "data" => "formatted.number",
                        "width" => "120px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Klas",
                        "data" => "linked.informatClass.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Getroffen leerling",
                        "data" => "linked.informatStudent.formatted.fullNameReversed"
                    ],
                    [
                        "title" => "Locatie",
                        "data" => "formatted.location",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Ongeval te wijten aan...",
                        "data" => "formatted.party",
                        "width" => "250px"
                    ],
                    [
                        "title" => "Aangegeven door",
                        "data" => "linked.creatorUser.formatted.fullNameReversed",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Aangegeven op",
                        "data" => "formatted.creationDateTime",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                ]
            );

            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->get($id)[0]);
    }

    protected function getStatus($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $statuses = $settings['status'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_statuses = [];

            foreach ($statuses as $k => $v) $_statuses[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_statuses);
        }
    }

    protected function getLocation($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $locations = $settings['location'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_optgroups = [];
            $_cateogries = [];

            foreach ($locations as $k => $v) {
                if ($v['sub']) {
                    $_optgroups[] = ["id" => $k, "name" => $v['name']];

                    foreach ($v['sub'] as $_k => $_v) $_cateogries[] = ['optgroup' => $k, 'optgroupName' => $v['name'], "id" => "{$k}-{$_k}", "name" => $_v];
                } else {
                    $_optgroups[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];
                    $_cateogries[] = ["optgroup" => SELECT_OTHER_ID, "id" => $k, ...$v];
                }
            }

            $this->appendToJson('optgroups', $_optgroups);
            $this->appendToJson('items', $_cateogries);
        }
    }

    protected function getParty($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $parties = $settings['parties'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_parties = [];

            foreach ($parties as $k => $v) $_parties[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_parties);
        }
    }

    protected function getSettings($view)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
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
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;
        $repo = new Accident;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $informatSubgroupId = Helpers::input()->post('informatSubgroupId')->getValue();
        $informatStudentId = Helpers::input()->post('informatStudentId')->getValue();
        $datetime = Helpers::input()->post('datetime')->getValue();
        $description = Helpers::input()->post('description')->getValue();
        $location = Helpers::input()->post('location')->getValue();
        $exactLocation = Helpers::input()->post('exactLocation')->getValue();
        $transport = Helpers::input()->post('transport')->getValue();
        $supervision = General::convert(Helpers::input()->post('supervision')->getValue(), "bool");
        $informatSupervisorId = Helpers::input()->post('informatSupervisorId')->getValue();
        $party = Helpers::input()->post('party')->getValue();
        $partyExternalName = Helpers::input()->post('partyExternalName')->getValue();
        $partyExternalFirstName = Helpers::input()->post('partyExternalFirstName')->getValue();
        $partyExternalSex = Helpers::input()->post('partyExternalSex')->getValue();
        $partyExternalStreet = Helpers::input()->post('partyExternalStreet')->getValue();
        $partyExternalNumber = Helpers::input()->post('partyExternalNumber')->getValue();
        $partyExternalBus = Helpers::input()->post('partyExternalBus')->getValue();
        $partyExternalZipcode = Helpers::input()->post('partyExternalZipcode')->getValue();
        $partyExternalCity = Helpers::input()->post('partyExternalCity')->getValue();
        $partyExternalCountryId = Helpers::input()->post('partyExternalCountryId')->getValue();
        $partyExternalCompany = Helpers::input()->post('partyExternalCompany')->getValue();
        $partyExternalPolicyNumber = Helpers::input()->post('partyExternalPolicyNumber')->getValue();
        $partyOtherFullName = Helpers::input()->post('partyOtherFullName')->getValue();
        $partyOtherFullAddress = Helpers::input()->post('partyOtherFullAddress')->getValue();
        $partyOtherBirthDay = Helpers::input()->post('partyOtherBirthDay')->getValue();
        $partyInstallReason = Helpers::input()->post('partyInstallReason')->getValue();
        $police = General::convert(Helpers::input()->post('police')->getValue(), "bool");
        $policeName = Helpers::input()->post('policeName')->getValue();
        $policePVNumber = Helpers::input()->post('policePVNumber')->getValue();
        $status = Helpers::input()->post("status");
        $informatStudentRelationId = Helpers::input()->post("informatStudentRelationId");
        $informatStudentEmailId = Helpers::input()->post("informatStudentEmailId");
        $informatStudentNumberId = Helpers::input()->post("informatStudentNumberId");
        $informatStudentBankId = Helpers::input()->post("informatStudentBankId");
        $informatStudentAddressId = Helpers::input()->post("informatStudentAddressId");
        $witnessId = Helpers::input()->post('witnessId');

        if (!$id) {
            if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatSubgroupId, Input::INPUT_TYPE_INT) || Input::empty($informatSubgroupId)) $this->setValidation("informatSubgroupId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatStudentId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentId)) $this->setValidation("informatStudentId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($datetime) || Input::empty($datetime)) $this->setValidation("datetime", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($location) || Input::empty($location)) $this->setValidation("location", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($party) || Input::empty($party)) $this->setValidation("party", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($description) || Input::empty($description)) $this->setValidation("description", state: self::VALIDATION_STATE_INVALID);
        }

        if (Arrays::first(explode("-", $location)) == "O") {
            if (!Input::check($exactLocation) || Input::empty($exactLocation)) $this->setValidation("exactLocation", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($transport) || Input::empty($transport)) $this->setValidation("transport", state: self::VALIDATION_STATE_INVALID);
        }

        if ($party == "E") {
            if (!Input::check($partyExternalName) || Input::empty($partyExternalName)) $this->setValidation("partyExternalName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalFirstName) || Input::empty($partyExternalFirstName)) $this->setValidation("partyExternalFirstName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalSex) || Input::empty($partyExternalSex)) $this->setValidation("partyExternalSex", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalStreet) || Input::empty($partyExternalStreet)) $this->setValidation("partyExternalStreet", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalNumber) || Input::empty($partyExternalNumber)) $this->setValidation("partyExternalNumber", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalZipcode) || Input::empty($partyExternalZipcode)) $this->setValidation("partyExternalZipcode", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalCity) || Input::empty($partyExternalCity)) $this->setValidation("partyExternalCity", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalCountryId) || Input::empty($partyExternalCountryId)) $this->setValidation("partyExternalCountryId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyExternalCompany) || Input::empty($partyExternalCompany)) $this->setValidation("partyExternalCompany", state: self::VALIDATION_STATE_INVALID);
        } else if ($party == "O") {
            if (!Input::check($partyOtherFullName) || Input::empty($partyOtherFullName)) $this->setValidation("partyOtherFullName", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyOtherFullAddress) || Input::empty($partyOtherFullAddress)) $this->setValidation("partyOtherFullAddress", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($partyOtherBirthDay) || Input::empty($partyOtherBirthDay)) $this->setValidation("partyOtherBirthDay", state: self::VALIDATION_STATE_INVALID);
        } else {
            if (!Input::check($partyInstallReason) || Input::empty($partyInstallReason)) $this->setValidation("partyInstallReason", state: self::VALIDATION_STATE_INVALID);
        }

        if ($police) {
            if (!Input::check($policeName) || Input::empty($policeName)) $this->setValidation("policeName", state: self::VALIDATION_STATE_INVALID);
        }

        if ($supervision) {
            if (!Input::check($informatSupervisorId, Input::INPUT_TYPE_INT) || Input::empty($informatSupervisorId)) $this->setValidation("informatSupervisorId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($id) {
            if (!Input::check($informatStudentRelationId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentRelationId)) $this->setValidation("informatStudentRelationId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatStudentEmailId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentEmailId)) $this->setValidation("informatStudentEmailId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatStudentNumberId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentNumberId)) $this->setValidation("informatStudentNumberId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatStudentBankId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentBankId)) $this->setValidation("informatStudentBankId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($informatStudentAddressId, Input::INPUT_TYPE_INT) || Input::empty($informatStudentAddressId)) $this->setValidation("informatStudentAddressId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $accident = $id ? Arrays::firstOrNull($repo->get($id)) : (new ObjectAccident);
            if (!$accident->number) $accident->number = $settings['lastNumber'] + 1;
            if (!$accident->creatorUserId) $accident->creatorUserId = User::getLoggedInUser()->id;
            $accident->status = $status?->getValue() ?: "N";
            $accident->schoolId = $schoolId;
            $accident->informatSubgroupId = $informatSubgroupId;
            $accident->informatStudentId = $informatStudentId;
            if ($informatStudentRelationId) $accident->informatStudentRelationId = $informatStudentRelationId->getValue();
            if ($informatStudentEmailId) $accident->informatStudentEmailId = $informatStudentEmailId->getValue();
            if ($informatStudentNumberId) $accident->informatStudentNumberId = $informatStudentNumberId->getValue();
            if ($informatStudentBankId) $accident->informatStudentBankId = $informatStudentBankId->getValue();
            if ($informatStudentAddressId) $accident->informatStudentAddressId = $informatStudentAddressId->getValue();
            $accident->datetime = Clock::at($datetime)->format("Y-m-d H:i:s");
            $accident->description = $description;
            $accident->location = $location;
            $accident->exactLocation = $exactLocation;
            $accident->transport = $transport;
            $accident->supervision = $supervision;
            $accident->informatSupervisorId = $informatSupervisorId;
            $accident->witnessId = $witnessId?->getValue() ?: (new Employee)->getByInformatId(User::getLoggedInUser()->informatEmployeeId);
            $accident->party = $party;
            $accident->partyExternalName = $partyExternalName;
            $accident->partyExternalFirstName = $partyExternalFirstName;
            $accident->partyExternalSex = $partyExternalSex;
            $accident->partyExternalStreet = $partyExternalStreet;
            $accident->partyExternalNumber = $partyExternalNumber;
            $accident->partyExternalBus = $partyExternalBus;
            $accident->partyExternalZipcode = $partyExternalZipcode;
            $accident->partyExternalCity = $partyExternalCity;
            $accident->partyExternalCountryId = $partyExternalCountryId;
            $accident->partyExternalCompany = $partyExternalCompany;
            $accident->partyExternalPolicyNumber = $partyExternalPolicyNumber;
            $accident->partyOtherFullName = $partyOtherFullName;
            $accident->partyOtherFullAddress = $partyOtherFullAddress;
            $accident->partyOtherBirthDay = $partyOtherBirthDay;
            $accident->partyInstallReason = $partyInstallReason;
            $accident->police = $police;
            $accident->policeName = $policeName;
            $accident->policePVNumber = $policePVNumber;

            $repo->set($accident);

            if (!$id) {
                $navItem = Arrays::first($navRepo->get(Session::get("moduleSettingsId")));
                $navItem->settings['lastNumber']++;
                $navRepo->set($navItem, ['settings']);
            }

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDeclarationsPrint($view, $id = null)
    {
        if (!$id) $this->setToast("Geen aangifte geselecteerd!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $id = explode("_", $id);
            $arepo = new Accident;

            $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
            $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
            $filename = "Ongevallen - Aangiftes.zip";

            foreach ($id as $_id) {
                $item = $arepo->get($_id)[0];
                $item->linked->school->linked->address = Arrays::firstOrNull((new SchoolAddress)->getBySchoolId($item->schoolId));

                $saveFilename = $folder . "/{$_id}." . $settings['blancoForm']['ext'];
                $template = new TemplateProcessor(LOCATION_UPLOAD . "/" . $settings['blancoForm']['file']);

                foreach ($item->toArray(true) as $key => $value) $template->setValue("accident:{$key}", $value);
                foreach ($item->linked->school->toArray(true) as $key => $value) $template->setValue("school:{$key}", $value);
                foreach ($item->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
                foreach ($item->linked?->supervisor?->toArray(true) ?: [] as $key => $value) $template->setValue("supervisor:{$key}", $value);
                foreach ($item->linked?->witness?->toArray(true) ?: [] as $key => $value) $template->setValue("witness:{$key}", $value);
                foreach ($item->linked?->informatStudentAddress?->toArray(true) ?: [] as $key => $value) $template->setValue("student:address.{$key}", $value);
                foreach (Arrays::flattenKeysRecursively($settings) as $key => $value) $template->setValue("setting:{$key}", $value);
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

                foreach ($template->getVariables() as $var) $template->setValue($var, '/');
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
        $_settings = Helpers::input()->all();
        $file = Helpers::input()->file("blancoForm_original")[0];
        unset($_settings['blancoForm_original']);

        if ($file && $file->getSize() > 0) {
            FileSystem::CreateFolder(LOCATION_UPLOAD);
            $origFilename = $file->getFilename();
            $origExt = $file->getExtension();
            $newName = GUID::create() . "." . $file->getExtension();

            if ($file->move(LOCATION_UPLOAD . "/{$newName}")) {
                $_settings['blancoForm.original'] = $origFilename;
                $_settings['blancoForm.ext'] = $origExt;
                $_settings['blancoForm.file'] = $newName;
            }
        }

        $settings = [];
        foreach ($_settings as $k => $v) $settings[str_replace("_", ".", $k)] = $v;
        $settings = General::normalizeArray($settings);

        $repo = new Navigation;
        $item = Arrays::first($repo->get(Session::get("moduleSettingsId")));
        $item->settings = array_replace_recursive($item->settings, $settings);

        $repo->set($item, ['settings']);
        $this->setToast("De instellingen zijn opgeslagen!");
    }

    // Delete functions

    // Mail functions
}
