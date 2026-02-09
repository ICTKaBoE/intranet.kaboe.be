<?php

namespace Controllers\API;

use stdClass;
use Helpers\Form;
use Helpers\HTML;
use Helpers\Table;
use Security\User;
use Helpers\Filter;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Mail\Mail;
use Database\Repository\Order\Line;
use Database\Repository\Order\Order;
use Database\Repository\Order\Status;
use Database\Repository\Mail\Receiver;
use Database\Repository\Order\Category;
use Database\Repository\Order\Supplier;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Navigation\Setting;
use Database\Object\Order\Line as OrderLine;
use Database\Object\Order\Order as OrderOrder;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Order\Supplier as OrderSupplier;

class OrderController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "order";

    // Get Functions
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

    protected function getAll($view, $id = null)
    {
        $repo = new Order;
        $filters = Filter::Find(['status', 'acceptorUserId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAllLine($view, $id = null)
    {
        $pRepo = new Order;
        $orderIds = Arrays::filter(explode(";", Helpers::url()->getParam("orderId")), fn($i) => Strings::isNotBlank($i));
        $orderIds = Arrays::map($orderIds, fn($p) => Arrays::first($pRepo->get($p))->id);

        $repo = new Line;
        $filters = [
            'orderId' => $orderIds,
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[3, 'asc'], [5, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "#",
                        "data" => "amount",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Wat",
                        "data" => "formatted.category"
                    ],
                    [
                        "title" => "Verduidelijking",
                        "data" => "clarifycation"
                    ],
                    [
                        "title" => "Toestel",
                        "data" => "formatted.asset",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Offerteprijs (per stuk)",
                        "data" => "formatted.quotePrice",
                        "width" => "200px"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAccept($view, $id = null)
    {
        $repo = new Order;
        $filters = Filter::Find(['status', 'acceptorUserId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getAcceptLine($view, $id = null)
    {
        $pRepo = new Order;
        $orderIds = Arrays::filter(explode(";", Helpers::url()->getParam("orderId")), fn($i) => Strings::isNotBlank($i));
        $orderIds = Arrays::map($orderIds, fn($p) => Arrays::first($pRepo->get($p))->id);

        $repo = new Line;
        $filters = [
            'orderId' => $orderIds,
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[2, 'asc'], [4, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "#",
                        "data" => "amount",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Wat",
                        "data" => "formatted.category"
                    ],
                    [
                        "title" => "Verduidelijking",
                        "data" => "clarifycation"
                    ],
                    [
                        "title" => "Toestel",
                        "data" => "formatted.asset",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Offerteprijs (per stuk)",
                        "data" => "formatted.quotePrice",
                        "width" => "200px"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getSupplier($view, $id = null)
    {
        $repo = new Supplier;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getSettings($view, $id = null)
    {
        $this->getNavigationSettings();
    }

    protected function getQuotes($view, $id = null)
    {
        $repo = new Order;
        if (!$id) $id = Helpers::url()->getParam('orderId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $order = $repo->getById($id);
            $quotes = [];
            if ($order->quoteLink) $quotes[] = $order->quoteLink;
            if (FileSystem::PathExists(LOCATION_FILES . "/order/{$order->guid}.pdf")) $quotes[] = "{$order->guid}.pdf";

            if (!$quotes) $this->appendToJson('raw', 'Geen bestanden!');
            else {
                $items = Arrays::map($quotes, function ($a) use ($order) {
                    $item = new stdClass;
                    $item->link = HTML::Link(HTML::LINK_TYPE_URL, Strings::startsWith("http", $a) ? $a : FileSystem::GetDownloadLink(LOCATION_FILES . "/order/{$order->guid}.pdf"), $a, HTML::LINK_TARGET_BLANK);

                    return $item;
                });

                $this->appendToJson('raw', General::processTemplate($items));
            }
        }
    }

    // Post functions
    protected function postAccept($view, $id = null)
    {
        $this->postAll($view, $id);
    }

    protected function postAll($view, $id = null)
    {
        if ($id == "add") $id = null;

        $repo = new Order;

        $_fields = [
            "status",
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "acceptorUserId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "supplierId",
            "quoteLink",
            "quoteFile" => ["type" => "file"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Order;

            $item = $repo->getById($id) ?? new OrderOrder;
            $item->fillWithPostData();
            if (!$id) $item->creatorUserId = User::getLoggedInUser()->id;
            $item->quoteLink = $fields["quoteLink"] ?? $item->quoteLink;

            $newId = $repo->set($item);
            if (!$id) $item->id = $newId;
            $item = $repo->getById($item->id);

            if ($fields["quoteFile"][0] && $fields["quoteFile"][0]->getSize() > 0) {
                $location = LOCATION_FILES . "/order";
                FileSystem::CreateFolder($location);

                if ($fields["quoteFile"][0]->move("{$location}/{$item->guid}." . $fields["quoteFile"][0]->getExtension())) {
                    $item->quoteFile = "{$item->guid}." . $fields["quoteFile"][0]->getExtension();
                    $repo->set($item);
                }
            }

            // Mail
            if ($item->linked->status->mailQuote) $this->mailQuote($item->id);
            else if ($item->linked->status->mailAccept) $this->mailAccept($item->id);
            else if ($item->linked->status->mailStatus) $this->mailStatus($item->id);
            else if ($item->linked->status->mailOrder) $this->mailOrder($id);

            if (!$id) $this->setRedirect("/../{$item->guid}");
            else $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postAllLine($view, $id = null)
    {
        $_fields = [
            "orderId",
            "amount" => ["mandatory" => true],
            "category" => ["mandatory" => true],
            "assetId" => ["type" => Input::INPUT_TYPE_INT],
            "clarifycation",
            "quotePrice" => ["type" => Input::INPUT_TYPE_FLOAT],
            "quoteVatIncluded" => ["type" => Input::INPUT_TYPE_BOOL, "convert" => "bool"],
            "warrenty" => ["type" => Input::INPUT_TYPE_BOOL, "convert" => "bool"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            if (!Arrays::contains(["O"], Arrays::first(explode("-", $fields["category"])))) {
                if (!Input::check($fields["assetId"]) || Input::empty($fields["assetId"])) $this->setValidation("assetId", self::VALIDATION_STATE_INVALID);
            } else {
                if (!Input::check($fields["clarifycation"]) || Input::empty($fields["clarifycation"])) $this->setValidation("clarifycation", self::VALIDATION_STATE_INVALID);
            }

            if ($this->validationIsAllGood()) {
                $pRepo = new Order;
                $order = $pRepo->getById($fields["orderId"]);

                $repo = new Line;
                $line = $repo->getById($id) ?? new OrderLine;
                $line->fillWithPostData();
                $line->orderId = $order->id;
                $line->assetId = Arrays::contains(["O"], Arrays::first(explode("-", $fields["category"]))) ? "" : $fields["assetId"];
                $repo->set($line);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De lijn is opgeslagen!");
            $this->setCloseModal();
            $this->setReloadTable();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postOrderRequestQuote($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->status = "QR";
            $repo->set($item);

            $this->mailQuote($item->id);
            $this->setToast("Offerte aangevraagd voor bon #{$item->formatted->number}");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }


    protected function postOrderRequestAccept($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->status = "WA";
            $repo->set($item);

            $this->mailAccept($item->id);
            $this->setToast("Goedkeuring aangevraagd voor bon #{$item->formatted->number}");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function postOrderOrder($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->status = "O";
            $repo->set($item);

            $this->mailOrder($item->id);
            $this->setToast("Bestelling geplaatst voor bon #{$item->formatted->number}");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function postSupplier($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "name" => ["mandatory" => true],
            "contactName" => ["mandatory" => true],
            "email" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_EMAIL],
            "phone",
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Supplier;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new OrderSupplier;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSettings($view, $id = null)
    {
        $this->postNavigationSettings();
    }

    // Delete functions     
    protected function deleteOrder($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De bon '{$item->formatted->number}' is verwijderd!");
        }
    }

    protected function deleteOrderLine($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Line;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De lijn '{$item->id}' is verwijderd!");
        }
    }

    protected function deleteSupplier($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Supplier;
        $pRepo = new Order;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($pRepo->getBySupplierId($item->id))) {
                $this->setToast("De leverancier '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan bestellingen!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De leverancier '{$item->name}' is verwijderd!");
        }

        $this->setCloseModal();
        $this->setReloadTable();
    }

    // Mail functions
    protected function mailQuote($id)
    {
        $repo = new Order;
        $lRepo = new Line;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.quote.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.quote.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        if (Strings::contains($body, "{{table}}")) {
            $lines = $lRepo->getByOrderId($h->id);

            $table = "  <table border='1' style='border-collapse: collapse; width: 100%'>
                            <thead>
                                <tr>
                                    <th style='text-align:left; padding: 3px 6px; width: 100px'>Aantal</th>
                                    <th style='text-align:left; padding: 3px 6px'>Wat</th>
                                    <th style='text-align:left; padding: 3px 6px'>Verduidelijking</th>
                                    <th style='text-align:left; padding: 3px 6px'>Toestel</th>
                                    <th style='text-align:left; padding: 3px 6px; width: 100px'>Garantiegeval?</th>
                                </tr>
                            </thead>
                            <tbody>";

            foreach ($lines as $line) {
                $table .= "     <tr>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->amount}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->formatted->category}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->clarifycation}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->formatted->asset}</td>
                                    <td style='text-align:left; padding: 3px 6px; background-color: " . ($line->warrenty ? 'green' : 'red') . "'>" . ($line->warrenty ? 'Ja' : 'Nee') . "</td>
                                </tr>";
            }

            $table .=   "   </tbody>
                        </table>";
            $body = str_replace("{{table}}", $table, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.quote.reply")->value, "bool")) $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->supplier->email;
        $receiver->name = $h->linked->supplier->formatted->contactWithName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailOrder($id)
    {
        $repo = new Order;
        $lRepo = new Line;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.order.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.order.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        if (Strings::contains($body, "{{table}}")) {
            $lines = $lRepo->getByOrderId($h->id);

            $table = "  <table border='1' style='border-collapse: collapse; width: 100%'>
                            <thead>
                                <tr>
                                    <th style='text-align:left; padding: 3px 6px; width: 100px'>Aantal</th>
                                    <th style='text-align:left; padding: 3px 6px'>Wat</th>
                                    <th style='text-align:left; padding: 3px 6px'>Verduidelijking</th>
                                    <th style='text-align:left; padding: 3px 6px'>Toestel</th>
                                    <th style='text-align:left; padding: 3px 6px; width: 100px'>Offerteprijs</th>
                                </tr>
                            </thead>
                            <tbody>";

            foreach ($lines as $line) {
                $table .= "     <tr>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->amount}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->formatted->category}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->clarifycation}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->formatted->asset}</td>
                                    <td style='text-align:left; padding: 3px 6px'>{$line->formatted->quotePrice}</td>
                                </tr>";
            }

            $table .=   "   </tbody>
                        </table>";
            $body = str_replace("{{table}}", $table, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.order.reply")->value, "bool")) $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->supplier->email;
        $receiver->name = $h->linked->supplier->formatted->contactWithName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailAccept($id)
    {
        $repo = new Order;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.accept.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.accept.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.accept.reply")->value, "bool")) $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->acceptorUser->username;
        $receiver->name = $h->linked->acceptorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }

    protected function mailStatus($id)
    {
        $repo = new Order;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $settingsRepo = new Setting;

        $h = $repo->getById($id);
        $mail = new MailMail;

        $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.status.subject")->value;
        $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.status.body")->value;

        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.status.reply")->value, "bool")) $mail->replyTo = [
            "email" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.email")->value,
            "name" => $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.reply.name")->value,
        ];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }
}
