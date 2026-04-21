<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\COLTDAlert\COLTDAlert as COLTDAlertCOLTDAlert;
use Database\Object\COLTDAlert\COLTDAlertMessage as COLTDAlertCOLTDAlertMessage;
use Database\Object\COLTDAlert\Group as COLTDAlertGroup;
use Database\Object\COLTDAlert\GroupMember as COLTDAlertGroupMember;
use Database\Object\COLTDAlert\Template as COLTDAlertTemplate;
use Database\Object\Navigation\TableDef;
use Database\Repository\COLTDAlert\COLTDAlert;
use Database\Repository\COLTDAlert\COLTDAlertMessage;
use Database\Repository\COLTDAlert\Group;
use Database\Repository\COLTDAlert\GroupMember;
use Database\Repository\COLTDAlert\Template;
use Database\Repository\Informat\EmployeeNumber;
use Database\Repository\Navigation\Setting as NavigationSetting;
use Database\Repository\Setting\Setting;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use RingRing\Client;
use RingRing\Model\Request\CancelRequest;
use RingRing\Model\Request\MessageRequest;
use Security\Input;

class ColtdAlertController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "coltdAlert";

    // Get
    protected function getTemplates($view, $id = null)
    {
        $repo = new Template;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find([]);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get();
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getGroups($view, $id = null)
    {
        $repo = new Group;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find([]);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get();
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $group = $repo->getById($id);

            if ($group) {
                $members = (new GroupMember)->getByGroupId($group->id) ?: [];
                $group->members = join(";", Arrays::map($members, fn($m) => $m->informatEmployeeNumberId));
            }

            $this->appendToJson('fields', $group);
        }
    }

    protected function getLogs($view, $id = null)
    {
        $repo = new COLTDAlert;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find([]);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get();
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getLogsMessages($view, $id)
    {
        $item = (new COLTDAlert)->getById($id);
        $repo = new COLTDAlertMessage;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format([
                new TableDef([
                    "order" => 1,
                    "title" => "Status",
                    "data" => "formatted.status",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 200
                ]),
                new TableDef([
                    "order" => 2,
                    "title" => "Ontvanger",
                    "data" => "to",
                    "orderable" => false,
                    "searchable" => true,
                    "width" => 0
                ]),
                new TableDef([
                    "order" => 3,
                    "title" => "Ingepland",
                    "data" => "formatted.timeScheduled",
                    "orderable" => true,
                    "searchable" => false,
                    "width" => 200,
                    "render" => true,
                    "defaultOrder" => true,
                    "defaultOrderOrder" => 1,
                    "defaultOrderDirection" => "asc"
                ]),
                new TableDef([
                    "order" => 4,
                    "title" => "Afgeleverd",
                    "data" => "formatted.timeDelivered",
                    "orderable" => true,
                    "searchable" => false,
                    "width" => 200,
                    "render" => true,
                ])
            ], false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->getByColtdalertId($item->id);
            $this->appendToJson("rows", $items);
        }
    }

    protected function getNumbers($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson("items", Arrays::map(explode(PHP_EOL, (new NavigationSetting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "numbers")->value), fn($n) => ["id" => $n, "name" => $n]));
        }
    }

    protected function getEmployeeNumber($view, $id = null)
    {
        $repo = new EmployeeNumber;
        $filters = Filter::Find(['category']);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    // Post
    protected function postSend($view, $id = null)
    {
        $repo = new COLTDAlert;
        $messageRepo = new COLTDAlertMessage;

        $_fields = [
            "from" => ["mandatory" => true],
            "groupId" => ["mandatory" => true],
            "useTemplate" => ["type" => Input::INPUT_TYPE_BOOL],
            "content" => ["mandatory" => true, "precondition" => ["useTemplate" => false]],
            "sendNow" => ["type" => Input::INPUT_TYPE_BOOL],
            "date" => ["mandatory" => true, "preconditions" => ["sendNow" => false]],
            "time" => ["mandatory" => true, "preconditions" => ["sendNow" => false]],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = new COLTDAlertCOLTDAlert;
            $item->fillWithPostData();
            if (!$fields['sendNow']) $item->datetime = "{$fields['date']} {$fields['time']}:00";
            $nId = $repo->set($item);

            $item = $repo->getById($nId);

            $groupMemberRepo = new GroupMember;
            $numbers = [];
            foreach (explode(";", $fields['groupId']) as $gId) $numbers[] = Arrays::map($groupMemberRepo->getByGroupId($gId), fn($gm) => $gm->linked->informatEmployeeNumber->number);
            $numbers = Arrays::flatten($numbers);
            $numbers = array_unique($numbers);
            $numbers = Arrays::map($numbers, fn($n) => General::removeLeadingZero(str_replace([" ", "+"], "", $n)));
            $numbers = Arrays::map($numbers, fn($n) => Strings::startsWith($n, "4") ? "32{$n}" : $n);
            $numbers = array_chunk($numbers, 1000);

            $client = new Client((new Setting)->getById("ringring.key")->value);

            try {
                foreach ($numbers as $_numbers) {
                    $options = [
                        "from" => $fields["from"],
                        "to" => implode(",", $_numbers),
                        "message" => $fields['content'],
                    ];

                    if (!$fields['sendNow']) $options["timeScheduled"] = "{$fields['date']} {$fields['time']}:00";
                    $result = $client->sendMessage(new MessageRequest($options));
                    $result = json_decode($result, true);

                    foreach ($result['Messages'] as $message) {
                        $mItem = new COLTDAlertCOLTDAlertMessage;
                        $mItem->coltdalertId = $item->id;
                        $mItem->ringringGuid = $message['MessageId'];
                        $mItem->to = $message['To'];
                        $messageRepo->set($mItem);
                    }
                }
            } catch (\Exception $e) {
                $this->setToast("Er deed zich een probleem voor met het verzenden van dit bericht.<br />{$e->getMessage()}", self::VALIDATION_STATE_INVALID);
            }
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postTemplates($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new Template;

        $_fields = [
            "name" => ["mandatory" => true],
            "content" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new COLTDAlertTemplate);
            $item->fillWithPostData();

            $repo->set($item);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postGroups($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new Group;

        $_fields = [
            "name" => ["mandatory" => true],
            "members",
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new COLTDAlertGroup);
            $item->fillWithPostData();

            $nId = $repo->set($item);
            if (!$item->id) $item->id = $nId;

            $gmRepo = new GroupMember;
            $gmRepo->delete(["groupId" => $item->id]);

            foreach (explode(";", $fields["members"]) as $member) {
                $gmRepo->set(new COLTDAlertGroupMember([
                    "groupId" => $item->id,
                    "informatEmployeeNumberId" => $member
                ]));
            }

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postLogsCancel($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new COLTDAlert;
        $messageRepo = new COLTDAlertMessage;
        $client = new Client((new Setting)->getById("ringring.key")->value);

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $messages = $messageRepo->getByColtdalertId($item->id);

            foreach ($messages as $message) {
                $result = $client->cancelMessage(new CancelRequest(["messageID" => $message->ringringGuid]));
                $result = json_decode($result, true);
            }

            $this->setToast("Het bericht gepland op {$item->formatted->datetime->display} is met success geannuleerd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
