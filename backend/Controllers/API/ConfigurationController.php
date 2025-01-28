<?php

namespace Controllers\API;

use Helpers\HTML;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\GeneralMessage as ObjectGeneralMessage;
use Database\Object\School as ObjectSchool;
use Database\Repository\Setting;
use Database\Repository\Navigation;
use Database\Repository\SecurityGroup;
use Database\Repository\GeneralMessage;
use Database\Repository\SecurityGroupUser;
use Database\Object\SecurityGroup as ObjectSecurityGroup;
use Database\Object\SecurityGroupUser as ObjectSecurityGroupUser;
use Database\Repository\School;
use Database\Repository\SettingTab;

class ConfigurationController extends ApiController
{
    // Get functions
    protected function getGeneral($view, $id = null)
    {
        $repo = new Setting;

        if (Strings::equal($view, self::VIEW_FORM)) {
            $settings = [];
            $items = $repo->get();

            foreach ($items as $item) $settings[$item->id] = $item->value;

            $this->appendToJson('fields', $settings);
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $settingTabs = (new SettingTab)->get();

            $tabs = Arrays::map($settingTabs, fn($t) => $t->formatted->html);
            $tabs = implode("", $tabs);

            foreach ($settingTabs as $tab) {
                $settings = (new Setting)->getBySettingTabId($tab->id);
                $settings = Arrays::filter($settings, fn($s) => $s->order > 0);
                $settings = Arrays::map($settings, fn($s) => $s->formatted->html);
                $settings = implode("", $settings);

                $tab->settings($settings);
            }

            $contents = Arrays::map($settingTabs, fn($t) => $t->formatted->contentHtml);
            $contents = implode("", $contents);

            $items = [
                [
                    "navtabs" => $tabs,
                    "contents" => $contents
                ]
            ];

            $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "&"));
        }
    }

    protected function getSchools($view, $id = null)
    {
        $repo = new School;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
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
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get();
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = Arrays::firstOrNull($repo->get($id));
            $this->appendToJson('fields', $group);
        }
    }

    protected function getGroups($view, $id = null)
    {
        $repo = new SecurityGroup;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
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
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => HTML::Icon("eye", "Read"),
                        "data" => "formatted.icon.read",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("plus", "Create"),
                        "data" => "formatted.icon.create",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("pencil", "Update"),
                        "data" => "formatted.icon.update",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("trash", "Delete"),
                        "data" => "formatted.icon.delete",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("file-export", "Export"),
                        "data" => "formatted.icon.export",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("settings", "Change Settings"),
                        "data" => "formatted.icon.changeSettings",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => HTML::Icon("cloud-network", "Administrator"),
                        "data" => "formatted.icon.admin",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ]
                ]
            );

            $items = $repo->get();
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = Arrays::firstOrNull($repo->get($id));
            $members = (new SecurityGroupUser)->getBySecurityGroupId($group->id);

            $group->members = join(";", Arrays::map($members, fn($m) => $m->userId));
            $this->appendToJson('fields', $group);
        }
    }

    protected function getMessagesType($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $statuses = $settings['messages']['type'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_statuses = [];

            foreach ($statuses as $k => $v) $_statuses[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_statuses);
        }
    }

    protected function getMessages($view, $id = null)
    {
        $repo = new GeneralMessage;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"], [1, "asc"]]);
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
                        "title" => "Type",
                        "data" => "mapped.type",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Module",
                        "data" => "linked.navigation.name",
                        "defaultContent" => "Algemeen"
                    ],
                    [
                        "title" => "Geldig van",
                        "data" => "formatted.from",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "200px"
                    ],
                    [
                        "title" => "Geldig tot",
                        "data" => "formatted.until",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "200px"
                    ]
                ]
            );

            $items = $repo->get();
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = Arrays::firstOrNull($repo->get($id));
            $this->appendToJson('fields', $group);
        }
    }

    // Post functions
    protected function postGeneral($view, $id = null)
    {
        $repo = new Setting;
        $settings = $repo->get();

        foreach ($settings as $setting) {
            $post = Helpers::input()->post(str_replace(".", "_", $setting->id));
            if (is_null($post)) {
                if ($setting->type == "switch") $post = "0";
                else $post = $setting->value;
            } else $post = $post->getValue();

            $setting->value = $post;
            $repo->set($setting);
        }

        $this->setToast("De instellingen zijn opgeslagen!");
        $this->handle();
    }

    protected function postSchools($view, $id = null)
    {
        if ($id == "add") $id = null;

        $name = Helpers::input()->post('name')->getValue();
        $color = Helpers::input()->post('color')->getValue();
        $intuneOrderIdPrefix = Helpers::input()->post('intuneOrderIdPrefix')->getValue();
        $jamfIpadPrefix = Helpers::input()->post('jamfIpadPrefix')->getValue();
        $adJobTitlePrefix = Helpers::input()->post('adJobTitlePrefix')->getValue();
        $adOuPart = Helpers::input()->post('adOuPart')->getValue();
        $adSecGroupPart = Helpers::input()->post('adSecGroupPart')->getValue();
        $syncUpdateMail = Helpers::input()->post('syncUpdateMail')->getValue();

        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new School;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ObjectSchool;
                $item->name = $name;
                $item->color = $color;
                $item->intuneOrderIdPrefix = $intuneOrderIdPrefix;
                $item->jamfIpadPrefix = $jamfIpadPrefix;
                $item->adJobTitlePrefix = $adJobTitlePrefix;
                $item->adOuPart = $adOuPart;
                $item->adSecGroupPart = $adSecGroupPart;
                $item->syncUpdateMail = $syncUpdateMail;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postGroups($view, $id = null)
    {
        if ($id == "add") $id = null;

        $name = Helpers::input()->post('name')->getValue();
        $read = Helpers::input()->post('read')->getValue();
        $create = Helpers::input()->post('create')->getValue();
        $update = Helpers::input()->post('update')->getValue();
        $delete = Helpers::input()->post('delete')->getValue();
        $export = Helpers::input()->post('export')->getValue();
        $changeSettings = Helpers::input()->post('changeSettings')->getValue();
        $admin = Helpers::input()->post('admin')->getValue();
        $m365GroupId = Helpers::input()->post('m365GroupId')->getValue();
        $members = Helpers::input()->post('members')->getValue();

        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new SecurityGroup;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ObjectSecurityGroup;
                $item->name = $name;
                $item->permission = [(int)General::convert($read, "bool"), (int)General::convert($create, "bool"), (int)General::convert($update, "bool"), (int)General::convert($delete, "bool"), (int)General::convert($export, "bool"), (int)General::convert($changeSettings, "bool"), (int)General::convert($admin, "bool")];
                $item->m365GroupId = $m365GroupId;

                $newId = $repo->set($item);
                if (!$item->id) $item->id = $newId;

                $sguRepo = new SecurityGroupUser;
                $sguRepo->delete(["securityGroupId" => $item->id]);

                foreach (explode(";", $members) as $member) {
                    $sguRepo->set(new ObjectSecurityGroupUser([
                        "securityGroupId" => $item->id,
                        "userId" => $member
                    ]));
                }
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De gebruikersgroep is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMessages($view, $id = null)
    {
        if ($id == "add") $id = null;

        $from = Helpers::input()->post('from')->getValue();
        $until = Helpers::input()->post('until')->getValue();
        $type = Helpers::input()->post('type')->getValue();
        $navigationId = Helpers::input()->post('navigationId')->getValue();
        $content = Helpers::input()->post('content')->getValue();

        if (!Input::check($from) || Input::empty($from)) $this->setValidation("from", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new GeneralMessage;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ObjectGeneralMessage;
                $item->from = str_replace("T", " ", $from);
                $item->until = str_replace("T", " ", $until) ?: null;
                $item->type = $type;
                $item->navigationId = $navigationId;
                $item->content = $content;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De afstand is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions
    protected function deleteGroups($view, $id)
    {
        $id = explode("_", $id);
        $repo = new SecurityGroup;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De gebruikersgroep '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteMessages($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new GeneralMessage;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het bericht is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
