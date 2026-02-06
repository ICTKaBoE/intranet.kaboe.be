<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Table;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\User\User;
use Database\Repository\Setting\Tab;
use Database\Repository\School\School;
use Database\Repository\Security\Group;
use Database\Repository\General\Message;
use Database\Repository\Setting\Setting;
use Database\Repository\User\LoginHistory;
use Database\Repository\Security\GroupUser;
use Database\Repository\General\MessageType;
use Database\Repository\Navigation\Navigation;
use Database\Object\Route\Group as ObjectSchool;
use Database\Repository\Security\GroupNavigation;
use Database\Object\General\Message as GeneralMessage;
use Database\Object\School\Course as SchoolCourse;
use Database\Object\School\Department as SchoolDepartment;
use Database\Object\School\Hour as SchoolHour;
use Database\Object\Security\Group as ObjectSecurityGroup;
use Database\Object\Security\GroupUser as ObjectSecurityGroupUser;
use Database\Object\Security\GroupNavigation as SecurityGroupNavigation;
use Database\Repository\School\Course;
use Database\Repository\School\Department;
use Database\Repository\School\Hour;

class ConfigurationController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "configuration";

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
            $settingTabs = (new Tab)->get();

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
        } else if (Strings::equal($view, self::VIEW_PS)) {
            $items = $repo->get();
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray()));
        }
    }

    protected function getSchools($view, $id = null)
    {
        $repo = new School;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
            $this->appendToJson('fields', $group);
        }
    }

    protected function getDepartment($view, $id = null)
    {
        $repo = new Department;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
            $this->appendToJson('fields', $group);
        }
    }

    protected function getCourse($view, $id = null)
    {
        $repo = new Course;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
            $this->appendToJson('fields', $group);
        }
    }

    protected function getHours($view, $id = null)
    {
        $repo = new Hour;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
            $this->appendToJson('fields', $group);
        }
    }

    protected function getUsers($view, $id = null)
    {
        $repo = new User;
        $loginRepo = new LoginHistory;

        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            Arrays::each($items, function ($i) use ($loginRepo) {
                $lastLogin = $loginRepo->getByUserId($i->id);
                $lastLogin = Arrays::firstOrNull($lastLogin);

                $i->formatted->lastLogin = $lastLogin ? Clock::at($lastLogin->timestamp)->plusHours(1)->format("d/m/Y H:i:s") . " (" . (Strings::equal($lastLogin->source, "local") ? "Lokaal" : "Office 365") . ")" : null;
            });
            $this->appendToJson("rows", $items);
        }
    }

    protected function getGroups($view, $id = null)
    {
        $repo = new Group;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
            $members = (new GroupUser)->getBySecurityGroupId($group->id);
            $applications = Arrays::filter((new GroupNavigation)->getBySecurityGroupId($group->id), fn($i) => $i->linked->navigation->type == "P");
            $links = Arrays::filter((new GroupNavigation)->getBySecurityGroupId($group->id), fn($i) => $i->linked->navigation->type == "L");

            $group->members = join(";", Arrays::map($members, fn($m) => $m->userId));
            $group->applications = join(";", Arrays::map($applications, fn($a) => $a->navigationId));
            $group->links = join(";", Arrays::map($links, fn($a) => $a->navigationId));
            $this->appendToJson('fields', $group);
        }
    }

    protected function getMessagesType($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new MessageType)->get());
        }
    }

    protected function getMessages($view, $id = null)
    {
        $repo = new Message;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);
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

        $_fields = [
            "name" => ["mandatory" => true],
            "virtual",
            "parentSchoolId" => ['default' => 0, 'trimToNull' => true],
            "color",
            "import",
            "sync",
            "syncEmployeeCompanyName",
            "syncStudentCompanyName",
            "syncEmployeeOU",
            "syncStudentOU",
            "intuneOrderIdPrefix",
            "jamfIpadPrefix",
            "adJobTitlePrefix",
            "adOuPart",
            "adSecGroupPart",
            "syncUpdateMail"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new School;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ObjectSchool;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postDepartment($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "name" => ["mandatory" => true],
            "schoolId" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Department;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SchoolDepartment;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postCourse($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true],
            "departmentId" => ["mandatory" => true],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Course;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SchoolCourse;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postHours($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true],
            "start" => ["mandatory" => true],
            "end" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Hour;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SchoolHour;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postGroups($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "name" => ["mandatory" => true],
            "m365GroupId",
            "members",
            "applications",
            "links"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Group;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ObjectSecurityGroup;
                $item->fillWithPostData();

                $newId = $repo->set($item);
                if (!$item->id) $item->id = $newId;

                $sguRepo = new GroupUser;
                $sguRepo->delete(["securityGroupId" => $item->id]);

                foreach (explode(";", $fields["members"]) as $member) {
                    $sguRepo->set(new ObjectSecurityGroupUser([
                        "securityGroupId" => $item->id,
                        "userId" => $member
                    ]));
                }

                $sgnRepo = new GroupNavigation;
                $sgnRepo->delete(["securityGroupId" => $item->id]);

                if ($fields["applications"]) {
                    $navRepo = new Navigation;

                    foreach (explode(";", $fields["applications"]) as $application) {
                        $navItem = $navRepo->getById($application);

                        $sgnRepo->set(new SecurityGroupNavigation([
                            "securityGroupId" => $item->id,
                            "navigationId" => $navItem->id
                        ]));

                        while ($navItem->parentId !== 0) {
                            $navItem = $navRepo->getById($navItem->parentId);
                            $doesExists = $sgnRepo->get(filters: ['securityGroupId' => $item->id, "navigationId" => $navItem->id]);

                            if (!$doesExists) {
                                $sgnRepo->set(new SecurityGroupNavigation([
                                    "securityGroupId" => $item->id,
                                    "navigationId" => $navItem->id
                                ]));
                            }
                        }
                    }
                }

                if ($fields["links"]) {
                    $navRepo = new Navigation;

                    foreach (explode(";", $fields["links"]) as $link) {
                        $navItem = $navRepo->getById($link);

                        $sgnRepo->set(new SecurityGroupNavigation([
                            "securityGroupId" => $item->id,
                            "navigationId" => $navItem->id
                        ]));

                        while ($navItem->parentId !== 0) {
                            $navItem = $navRepo->getById($navItem->parentId);
                            $doesExists = $sgnRepo->get(filters: ['securityGroupId' => $item->id, "navigationId" => $navItem->id]);

                            if (!$doesExists) {
                                $sgnRepo->set(new SecurityGroupNavigation([
                                    "securityGroupId" => $item->id,
                                    "navigationId" => $navItem->id
                                ]));
                            }
                        }
                    }
                }
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMessages($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "from" => ["mandatory" => true],
            "until" => ["default" => null],
            "type" => ["mandatory" => true],
            "navigationId" => ["type" => Input::INPUT_TYPE_INT],
            "content" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Message;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new GeneralMessage;
                $item->fillWithPostData();
                $item->from = str_replace("T", " ", $item->from);
                $item->until = str_replace("T", " ", $item->until) ?: null;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions
    protected function deleteGroups($view, $id)
    {
        $id = explode("_", $id);
        $repo = new Group;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
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
        $repo = new Message;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het bericht is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
