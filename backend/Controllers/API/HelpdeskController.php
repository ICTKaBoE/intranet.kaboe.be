<?php

namespace Controllers\API;

use stdClass;
use Helpers\Form;
use Helpers\HTML;
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
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\Helpdesk\Status;
use Database\Repository\Helpdesk\Thread;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Helpdesk\Category;
use Database\Repository\Helpdesk\Helpdesk;
use Database\Repository\Helpdesk\Priority;
use Database\Repository\Navigation\Setting;
use Database\Repository\Navigation\TableDef;
use Database\Repository\Navigation\Navigation;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Helpdesk\Thread as HelpdeskThread;
use Database\Object\Helpdesk\Helpdesk as HelpdeskHelpdesk;

class HelpdeskController extends ApiController
{
    // Get functions
    protected function getMine($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Helpdesk;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'creatorUserId' => $currentUserId,
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $navRepo = new Navigation;
            $navItem = $navRepo->getByParentIdAndLink($navRepo->getByParentIdAndLink(0, "helpdesk")->id, "mine");

            [$defaultOrder, $columns] = Table::Format((new TableDef)->getByNavigationId($navItem->id));
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getTickets($view, $id = null)
    {
        $repo = new Helpdesk;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $navRepo = new Navigation;
            $navItem = $navRepo->getByParentIdAndLink($navRepo->getByParentIdAndLink(0, "helpdesk")->id, "tickets");

            [$defaultOrder, $columns] = Table::Format((new TableDef)->getByNavigationId($navItem->id));
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAssigned($view, $id = null)
    {
        $currentUserId = User::getLoggedInUser()->id;
        $repo = new Helpdesk;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'assignedToUserId' => $currentUserId,
                'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $navRepo = new Navigation;
            $navItem = $navRepo->getByParentIdAndLink($navRepo->getByParentIdAndLink(0, "helpdesk")->id, "assigned");

            [$defaultOrder, $columns] = Table::Format((new TableDef)->getByNavigationId($navItem->id));
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getPriority($view, $id = null)
    {
        $repo = new Priority;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', $repo->get());
        }
    }

    protected function getStatus($view, $id = null)
    {
        $repo = new Status;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', $repo->get());
        }
    }

    protected function getCategory($view, $id = null)
    {
        $catRepo = new Category;

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

    protected function getThread($view, $id = null)
    {
        $threadRepo = new Thread;
        if (!$id) $id = Helpers::url()->getParam('ticketId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $ticket = (new Helpdesk)->getById($id);
            $items = Arrays::map($threadRepo->getByTicketId($ticket->id), fn($i) => $i->toArray(true));
            $this->appendToJson('raw', General::processTemplate($items));
        }
    }

    protected function getAttachments($view, $id = null)
    {
        $repo = new Helpdesk;
        if (!$id) $id = Helpers::url()->getParam('ticketId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $ticket = $repo->getById($id);
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
        $this->getNavigationSettings("helpdesk");
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

        $settingsRepo = new Setting;
        $navigation = (new Navigation)->getByParentIdAndLink(0, "helpdesk");

        $repo = new Helpdesk;
        $threadRepo = new Thread;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "priority",
            "status",
            "category" => ["mandatory" => true],
            "subject",
            "roomId" => ["mandatory" => false, 'type' => Input::INPUT_TYPE_INT],
            "assetId" => ["mandatory" => false, 'type' => Input::INPUT_TYPE_INT],
            "content",
            "assignedToUserId" => ["type" => Input::INPUT_TYPE_INT],
            "attachments" => ["type" => "file"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        $mailAssignedTo = false;

        if (!$id) {
            if (!Input::check($fields['content']) || Input::empty($fields['content'])) $this->setValidation("content", state: self::VALIDATION_STATE_INVALID);
        }

        if (Arrays::first(explode("-", $fields['category'])) !== "O") {
            if (!Input::check($fields['assetId'], Input::INPUT_TYPE_INT) || Input::empty($fields['assetId'])) $this->setValidation("assetId", state: self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $helpdesk = $repo->getById($id) ?? (new HelpdeskHelpdesk);
            $helpdesk->fillWithPostData();
            if (!$helpdesk->number) $helpdesk->number = $settingsRepo->getByNavigationIdAndKey($navigation->id, "lastNumber")->value + 1;
            if (!$helpdesk->creatorUserId) $helpdesk->creatorUserId = User::getLoggedInUser()->id;
            if ($fields['assignedToUserId']) {
                if ($helpdesk->assignedToUserId != $fields['assignedToUserId']) $mailAssignedTo = true;
                $helpdesk->assignedToUserId = $fields['assignedToUserId'];
            }
            $helpdesk->lastActionDateTime = Clock::nowAsString("Y-m-d H:i:s");

            $newId = $repo->set($helpdesk);
            if (!$id) $helpdesk->id = $newId;
            $helpdesk = $repo->getById($helpdesk->id);

            if ($fields['attachments']) {
                $location = LOCATION_UPLOAD . "/helpdesk/{$helpdesk->guid}";
                FileSystem::CreateFolder($location);

                foreach ($fields['attachments'] as $index => $attachment) {
                    $attachment->move("{$location}/{$helpdesk->guid}_{$index}." . $attachment->getExtension());
                }
            }

            if ($fields['content']) {
                $thread = new HelpdeskThread;
                $thread->ticketId = $helpdesk->id;
                $thread->creatorId = User::getLoggedInUser()->id;
                $thread->content = $fields['content'];

                $threadRepo->set($thread);

                if ($helpdesk->status == "C") {
                    $helpdesk->status = "O";
                    $repo->set($helpdesk);
                }
            }

            // Update settings
            if (!$id) {
                $settingItem = $settingsRepo->getByNavigationIdAndKey($navigation->id, "lastNumber");
                $settingItem->value++;
                $settingsRepo->set($settingItem);
            }

            // Mail
            if (!$id) $this->mailNew($helpdesk->id);
            else {
                if ($helpdesk->creatorUserId != User::getLoggedInUser()->id && $fields['content']) $this->mailUpdate($helpdesk->id);
                if ($mailAssignedTo) $this->mailAssigned($helpdesk->id);
                if ($helpdesk->assignedToUserId && $fields['content']) $this->mailAssignedUpdate($helpdesk->id);
            }

            if (!$id) $this->setRedirect("/../{$helpdesk->guid}");
            else $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings($view, $id = null)
    {
        $this->postNavigationSettings("helpdesk");
    }

    // Delete functions

    // Mail functions
    protected function mailNew($id)
    {
        $repo = new Helpdesk;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;
        $navigation = (new Navigation)->getByParentIdAndLink(0, "helpdesk");

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.new.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.new.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.new.reply")->value, "bool")) $mail->replyTo =   $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailUpdate($id)
    {
        $repo = new Helpdesk;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;
        $navigation = (new Navigation)->getByParentIdAndLink(0, "helpdesk");

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.update.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.update.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.update.reply")->value, "bool")) $mail->replyTo =   $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailAssigned($id)
    {
        $repo = new Helpdesk;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;
        $navigation = (new Navigation)->getByParentIdAndLink(0, "helpdesk");

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assigned.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assigned.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assigned.reply")->value, "bool"))   $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->assignedToUser->username;
        $receiver->name = $h->linked->assignedToUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailAssignedUpdate($id)
    {
        $repo = new Helpdesk;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;
        $navigation = (new Navigation)->getByParentIdAndLink(0, "helpdesk");

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assignedUpdate.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assignedUpdate.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.template.assignedUpdate.reply")->value, "bool")) $mail->replyTo =   $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey($navigation->id, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->assignedToUser->username;
        $receiver->name = $h->linked->assignedToUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }
}
