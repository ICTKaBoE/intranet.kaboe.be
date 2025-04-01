<?php

namespace Controllers\API;

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
use Database\Object\Mail\Mail as MailMail;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Helpers\HTML;
use Security\FileSystem;
use stdClass;

class HelpdeskController extends ApiController
{
    // Get functions
    protected function getMine($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Ticket;

        if (Strings::equal($view, self::VIEW_TABLE)) {
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
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->getByCreatorUserId($currentUserId);
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    protected function getTickets($view, $id = null)
    {
        $repo = new Ticket;

        if (Strings::equal($view, self::VIEW_TABLE)) {
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
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    protected function getAssigned($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Ticket;

        if (Strings::equal($view, self::VIEW_TABLE)) {
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
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "300px"
                    ],
                ]
            );

            $items = $repo->getByAssignedToUserId($currentUserId);
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    protected function getPriority($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $priorities = $settings['priority'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_priorities = [];

            foreach ($priorities as $k => $v) $_priorities[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_priorities);
        }
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

    protected function getCategory($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $categories = $settings['category'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
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

    protected function getThread($view, $id = null)
    {
        $threadRepo = new Thread;
        if (!$id) $id = Helpers::url()->getParam('ticketId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $ticketId = Arrays::first((new Ticket)->get($id))->id;
            $items = Arrays::map($threadRepo->getByTicketId($ticketId), fn($i) => $i->toArray(true));
            $this->appendToJson('raw', General::processTemplate($items));
        }
    }

    protected function getAttachments($view, $id = null)
    {
        $repo = new Ticket;
        if (!$id) $id = Helpers::url()->getParam('ticketId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $ticket = Arrays::first($repo->get($id));
            $attachments = FileSystem::getFiles(LOCATION_UPLOAD . "/helpdesk/{$ticket->guid}");

            if (!$attachments) $this->appendToJson('raw', 'Geen bestanden!');
            else {

                $items = Arrays::map($attachments, function ($a) use ($ticket) {
                    $item = new stdClass;
                    $item->link = HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink(LOCATION_UPLOAD . "/helpdesk/{$ticket->guid}/{$a}"), $a, HTML::LINK_TARGET_BLANK);

                    return $item;
                });

                $this->appendToJson('raw', General::processTemplate($items));
            }
        }
    }

    protected function getSettings($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    // Post functions
    protected function postMine($view, $id = null)
    {
        $this->postTicket($view, $id);
    }

    protected function postTickets($view, $id = null)
    {
        $this->postTicket($view, $id);
    }

    protected function postAssigned($view, $id = null)
    {
        $this->postTicket($view, $id);
    }

    protected function postTicket($view, $id)
    {
        if ($id == "add") $id = null;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;
        $repo = new Ticket;
        $threadRepo = new Thread;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $priority = Helpers::input()->post('priority');
        $status = Helpers::input()->post('status');
        $category = Helpers::input()->post('category')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $assetId = Helpers::input()->post('assetId')->getValue();
        $content = Helpers::input()->post('content')->getValue();
        $assignedToUserId = Helpers::input()->post('assignedToUserId');
        $attachments = Helpers::input()->file('attachments');

        $mailAssignedTo = false;

        if (!$id) {
            if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($category) || Input::empty($category)) $this->setValidation("category", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", state: self::VALIDATION_STATE_INVALID);
        }

        if (Arrays::first(explode("-", $category)) !== "O") {
            if (!Input::check($assetId, Input::INPUT_TYPE_INT) || Input::empty($assetId)) $this->setValidation("assetId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $helpdesk = $id ? Arrays::firstOrNull($repo->get($id)) : (new ObjectTicket);
            if (!$helpdesk->number) $helpdesk->number = $settings['lastNumber'] + 1;
            if (!$helpdesk->creatorUserId) $helpdesk->creatorUserId = User::getLoggedInUser()->id;
            if ($assignedToUserId?->getValue()) {
                if ($helpdesk->assignedToUserId != $assignedToUserId->getValue()) $mailAssignedTo = true;
                $helpdesk->assignedToUserId = $assignedToUserId->getValue();
            }
            $helpdesk->priority = $priority?->getValue() ?: null;
            $helpdesk->schoolId = $schoolId;
            $helpdesk->status = $status;
            $helpdesk->roomId = $roomId ?: null;
            $helpdesk->category = $category;
            $helpdesk->assetId = $assetId;
            $helpdesk->lastActionDateTime = Clock::nowAsString("Y-m-d H:i:s");

            $newId = $repo->set($helpdesk);
            if (!$id) $helpdesk->id = $newId;
            $helpdesk = Arrays::first($repo->get($helpdesk->id));

            if ($attachments) {
                $location = LOCATION_UPLOAD . "/helpdesk/{$helpdesk->guid}";
                FileSystem::CreateFolder($location);

                foreach ($attachments as $index => $attachment) {
                    $attachment->move("{$location}/{$helpdesk->guid}_{$index}." . $attachment->getExtension());
                }
            }

            if ($content) {
                $thread = new HelpdeskThread;
                $thread->ticketId = $helpdesk->id;
                $thread->creatorId = User::getLoggedInUser()->id;
                $thread->content = $content;

                $threadRepo->set($thread);

                if ($helpdesk->status == "C" && $thread->creatorId != $helpdesk->assignedToUserId) {
                    $helpdesk->status = "O";
                    $repo->set($helpdesk);
                }
            }

            // Update settings
            if (!$id) {
                $navItem = Arrays::first($navRepo->get(Session::get("moduleSettingsId")));
                $navItem->settings['lastNumber']++;
                $navRepo->set($navItem, ['settings']);
            }

            // Mail
            if (!$id) $this->mailNew($helpdesk->id);
            else {
                if ($helpdesk->creatorUserId != User::getLoggedInUser()->id && $content) $this->mailUpdate($helpdesk->id);
                if ($mailAssignedTo) $this->mailAssigned($helpdesk->id);
                if ($helpdesk->assignedToUserId && $content) $this->mailAssignedUpdate($helpdesk->id);
            }

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings($view, $id = null)
    {
        $_settings = Helpers::input()->all();
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
    protected function mailNew($id)
    {
        $repo = new Ticket;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['new']['subject'];
        $body = $settings['mail']['template']['new']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['new']['reply'], "bool")) $mail->replyTo = $settings['mail']['reply'];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailUpdate($id)
    {
        $repo = new Ticket;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['update']['subject'];
        $body = $settings['mail']['template']['update']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['update']['reply'], "bool")) $mail->replyTo = json_encode($settings['mail']['reply']);

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailAssigned($id)
    {
        $repo = new Ticket;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['assigned']['subject'];
        $body = $settings['mail']['template']['assigned']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['assigned']['reply'], "bool")) $mail->replyTo = json_encode($settings['mail']['reply']);

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->assignedToUser->username;
        $receiver->name = $h->linked->assignedToUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailAssignedUpdate($id)
    {
        $repo = new Ticket;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['assignedUpdate']['subject'];
        $body = $settings['mail']['template']['assignedUpdate']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['assignedUpdate']['reply'], "bool")) $mail->replyTo = json_encode($settings['mail']['reply']);

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->assignedToUser->username;
        $receiver->name = $h->linked->assignedToUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }
}
