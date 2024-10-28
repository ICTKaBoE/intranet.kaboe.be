<?php

namespace Controllers\API;

use Security\GUID;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;
use Database\Repository\Helpdesk\Thread;
use Database\Repository\Helpdesk\Ticket;
use Database\Object\Helpdesk\Ticket as ObjectTicket;
use Database\Object\Helpdesk\Thread as HelpdeskThread;

class HelpdeskController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, "mine")) $this->getMine($view, $id);
        else if (Strings::equal($what, "tickets")) $this->getTickets($view, $id);
        else if (Strings::equal($what, "assigned")) $this->getAssigned($view, $id);
        else if (Strings::equal($what, "priority")) $this->getPriority($view, $id);
        else if (Strings::equal($what, "category")) $this->getCategory($view, $id);
        else if (Strings::equal($what, "status")) $this->getStatus($view, $id);
        else if (Strings::equal($what, "thread")) $this->getThread($view, $id);
        else if (Strings::equal($what, "settings")) $this->getSettings($view, $id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($view, $what, $id = null)
    {
        if (Strings::equal($what, "mine") || Strings::equal($what, "tickets") || Strings::equal($what, "assigned")) $this->postTicket($id);
        else if (Strings::equal($what, "settings")) $this->postSettings();

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function delete($what, $id = null)
    {
        // if (Strings::equal($what, "mine")) $this->deleteTicket($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
        }
        $this->handle();
    }

    // Get functions
    private function getMine($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Ticket;

        if (Strings::equal($view, "table")) {
            $filters = [
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[8, "desc"]]);
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
                        "title" => "Leeftijd",
                        "data" => "formatted.age",
                        "width" => "150px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "formatted.badge.status",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "150px"
                    ],
                    [
                        "title" => "Prioriteit",
                        "data" => "formatted.badge.priority",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Onderwerp",
                        "data" => "formatted.subject"
                    ],
                    [
                        "title" => "Toegewezen aan",
                        "data" => "linked.assignedToUser.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Laatste activiteit",
                        "data" => "formatted.lastActivity",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->getByCreatorUserId($currentUserId);

            foreach ($filters as $key => $value) {
                if (!$value || empty($value)) continue;

                if (is_array($value)) $items = Arrays::filter($items, fn($i) => Arrays::contains($value, $i->$key));
                else $items = Arrays::filter($items, fn($i) => Strings::equal($i->$key, $value));
            }

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    private function getTickets($view, $id = null)
    {
        $repo = new Ticket;

        if (Strings::equal($view, "table")) {
            $filters = [
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[9, "desc"]]);
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
                        "title" => "Leeftijd",
                        "data" => "formatted.age",
                        "width" => "150px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "formatted.badge.status",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "150px"
                    ],
                    [
                        "title" => "Prioriteit",
                        "data" => "formatted.badge.priority",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Onderwerp",
                        "data" => "formatted.subject"
                    ],
                    [
                        "title" => "Aangemaakt door",
                        "data" => "linked.creatorUser.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Toegewezen aan",
                        "data" => "linked.assignedToUser.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Laatste activiteit",
                        "data" => "formatted.lastActivity",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->get();

            foreach ($filters as $key => $value) {
                if (!$value || empty($value)) continue;

                if (is_array($value)) $items = Arrays::filter($items, fn($i) => Arrays::contains($value, $i->$key));
                else $items = Arrays::filter($items, fn($i) => Strings::equal($i->$key, $value));
            }

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    private function getAssigned($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Ticket;

        if (Strings::equal($view, "table")) {
            $filters = [
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[8, "desc"]]);
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
                        "title" => "Leeftijd",
                        "data" => "formatted.age",
                        "width" => "150px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "formatted.badge.status",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "150px"
                    ],
                    [
                        "title" => "Prioriteit",
                        "data" => "formatted.badge.priority",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Onderwerp",
                        "data" => "formatted.subject"
                    ],
                    [
                        "title" => "Aangemaakt door",
                        "data" => "linked.creatorUser.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Laatste activiteit",
                        "data" => "formatted.lastActivity",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->getByAssignedToUserId($currentUserId);

            foreach ($filters as $key => $value) {
                if (!$value || empty($value)) continue;

                if (is_array($value)) $items = Arrays::filter($items, fn($i) => Arrays::contains($value, $i->$key));
                else $items = Arrays::filter($items, fn($i) => Strings::equal($i->$key, $value));
            }

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    private function getPriority($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $priorities = $settings['priority'];

        if (Strings::equal($view, "select")) {
            $_priorities = [];

            foreach ($priorities as $k => $v) $_priorities[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_priorities);
        }
    }

    private function getStatus($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $statuses = $settings['status'];

        if (Strings::equal($view, "select")) {
            $_statuses = [];

            foreach ($statuses as $k => $v) $_statuses[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_statuses);
        }
    }

    private function getCategory($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $categories = $settings['category'];

        if (Strings::equal($view, "select")) {
            $_optgroups = [];
            $_cateogries = [];

            foreach ($categories as $k => $v) {
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

    private function getThread($view, $id = null)
    {
        $threadRepo = new Thread;

        if (Strings::equal($view, "list")) {
            $ticketId = Arrays::first((new Ticket)->get($id))->id;
            $items = Arrays::map($threadRepo->getByTicketId($ticketId), fn($i) => $i->toArray(true));
            $this->appendToJson('items', $items);
        }
    }

    private function getSettings($view)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    // Post functions
    private function postTicket($id)
    {
        if ($id == "add") $id = null;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;
        $repo = new Ticket;
        $threadRepo = new Thread;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $priority = Helpers::input()->post('priority')->getValue();
        $status = Helpers::input()->post('status');
        $category = Helpers::input()->post('category')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $assetId = Helpers::input()->post('assetId')->getValue();
        $content = Helpers::input()->post('content')->getValue();
        $assignedToUserId = Helpers::input()->post('assignedToUserId');

        if (!$id) {
            if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($category) || Input::empty($category)) $this->setValidation("category", "Categorie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", "Beschrijving probleem moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        }

        if (Arrays::first(explode("-", $category)) !== "O") {
            if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($assetId, Input::INPUT_TYPE_INT) || Input::empty($assetId)) $this->setValidation("assetId", "Toestel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $helpdesk = $id ? Arrays::first($repo->get($id)) : (new ObjectTicket);
            if (!$helpdesk->number) $helpdesk->number = $settings['lastNumber'] + 1;
            if (!$helpdesk->creatorUserId) $helpdesk->creatorUserId = User::getLoggedInUser()->id;
            if ($assignedToUserId) $helpdesk->assignedToUserId = $assignedToUserId;
            $helpdesk->priority = $priority;
            $helpdesk->schoolId = $schoolId;
            $helpdesk->status = $status;
            $helpdesk->roomId = $roomId;
            $helpdesk->category = $category;
            $helpdesk->assetId = $assetId;
            $helpdesk->lastActionDateTime = Clock::nowAsString("Y-m-d H:i:s");

            $newId = $repo->set($helpdesk);
            if (!$id) $helpdesk->id = $newId;

            if ($content) {
                $thread = new HelpdeskThread;
                $thread->ticketId = $helpdesk->id;
                $thread->creatorId = User::getLoggedInUser()->id;
                $thread->content = $content;

                $threadRepo->set($thread);
            }

            // Update settings
            if (!$id) {
                $navItem = Arrays::first($navRepo->get(Session::get("moduleSettingsId")));
                $navItem->settings['lastNumber']++;
                $navItem->settings = json_encode($navItem->settings);
                $navRepo->set($navItem, ['settings']);
            }

            $this->setReturn();
        }
    }

    private function postSettings()
    {
        $_settings = Helpers::input()->all();
        $settings = [];
        foreach ($_settings as $k => $v) $settings[str_replace("_", ".", $k)] = $v;
        $settings = General::normalizeArray($settings);

        $repo = new Navigation;
        $item = Arrays::first($repo->get(Session::get("moduleSettingsId")));
        $item->settings = json_encode(array_replace_recursive($item->settings, $settings));

        $repo->set($item, ['settings']);
        $this->setToast("De instellingen zijn opgeslagen!");
    }
}
