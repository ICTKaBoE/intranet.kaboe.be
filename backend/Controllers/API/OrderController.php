<?php

namespace Controllers\API;

use stdClass;
use Helpers\HTML;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Mail\Mail;
use Database\Repository\Navigation;
use Database\Repository\Order\Line;
use Database\Repository\Order\Order;
use Database\Repository\Mail\Receiver;
use Database\Repository\Order\Supplier;
use Database\Object\Mail\Mail as MailMail;
use Database\Object\Order\Line as OrderLine;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Order\Supplier as OrderSupplier;

class OrderController extends ApiController
{
    // Get Functions
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

    protected function getOrder($view, $id = null)
    {
        $repo = new Order;
        $filters = [
            'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
            'acceptorUserId' => Arrays::filter(explode(";", Helpers::url()->getParam("acceptorUserId")), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "desc"]]);
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
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "formatted.badge.status",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Aangemaakt door",
                        "data" => "linked.creatorUser.formatted.fullName",
                        "width" => "150px",
                    ],
                    [
                        "title" => "Goed te keuren door",
                        "data" => "formatted.acceptor",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Leverancier",
                        "data" => "linked.supplier.name",
                        "width" => "150px",
                    ],
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getOrderLine($view, $id = null)
    {
        $pRepo = new Order;
        $orderIds = Arrays::filter(explode(";", Helpers::url()->getParam("orderId")), fn($i) => Strings::isNotBlank($i));
        $orderIds = Arrays::map($orderIds, fn($p) => Arrays::first($pRepo->get($p))->id);

        $repo = new Line;
        $filters = [
            'orderId' => $orderIds,
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, 'asc'], [5, "asc"]]);
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

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getAccept($view, $id = null)
    {
        $repo = new Order;
        $filters = [
            'status' => Arrays::filter(explode(";", Helpers::url()->getParam("status")), fn($i) => Strings::isNotBlank($i)),
            'acceptorUserId' => Arrays::filter(explode(";", Helpers::url()->getParam("acceptorUserId")), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "desc"]]);
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
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "formatted.badge.status",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Aangemaakt door",
                        "data" => "linked.creatorUser.formatted.fullName",
                        "width" => "150px",
                    ],
                    [
                        "title" => "Goed te keuren door",
                        "data" => "formatted.acceptor",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Leverancier",
                        "data" => "linked.supplier.name",
                        "width" => "150px",
                    ],
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
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
            $this->appendToJson("checkbox", false);
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

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getSupplier($view, $id = null)
    {
        $repo = new Supplier;
        $filters = [];

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
                        "title" => "Contactpersoon",
                        "data" => "contactName"
                    ],
                    [
                        "title" => "E-mail",
                        "data" => "email",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Telefoon",
                        "data" => "phone",
                        "width" => "150px"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getSettings($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    protected function getQuotes($view, $id = null)
    {
        $repo = new Order;
        if (!$id) $id = Helpers::url()->getParam('orderId');

        if (Strings::equal($view, self::VIEW_LIST)) {
            $order = Arrays::first($repo->get($id));
            $quotes = [];
            if ($order->quoteLink) $quotes[] = $order->quoteLink;
            if (FileSystem::PathExists(LOCATION_UPLOAD . "/order/{$order->guid}.pdf")) $quotes[] = "{$order->guid}.pdf";

            if (!$quotes) $this->appendToJson('raw', 'Geen bestanden!');
            else {
                $items = Arrays::map($quotes, function ($a) use ($order) {
                    $item = new stdClass;
                    $item->link = HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink(LOCATION_UPLOAD . "/order/{$order->guid}.pdf"), $a, HTML::LINK_TARGET_BLANK);

                    return $item;
                });

                $this->appendToJson('raw', General::processTemplate($items));
            }
        }
    }

    // Post functions
    protected function postAccept($view, $id = null)
    {
        $this->postOrder($view, $id);
    }

    protected function postOrder($view, $id = null)
    {
        if ($id == "add") $id = null;

        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $status = Helpers::input()->post('status')->getValue();
        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $acceptorUserId = Helpers::input()->post('acceptorUserId')->getValue();
        $supplierId = Helpers::input()->post('supplierId')->getValue();
        $quoteLink = Helpers::input()->post("quoteLink")?->getValue();
        $quoteFile = Helpers::input()->file("quoteFile")[0];

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($acceptorUserId) || Input::empty($acceptorUserId)) $this->setValidation("acceptorUserId", "Goed te keuren door moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Order;

            $item = $id ? Arrays::first($repo->get($id)) : new Order;
            if (!$item->number) $item->number = $settings['lastNumber'] + 1;
            if (!$id) $item->creatorUserId = User::getLoggedInUser()->id;
            $item->status = $status;
            $item->schoolId = $schoolId;
            $item->acceptorUserId = $acceptorUserId;
            $item->supplierId = $supplierId;
            $item->quoteLink = $quoteLink;

            $newId = $repo->set($item);
            if (!$id) $item->id = $newId;
            $item = Arrays::first($repo->get($item->id));

            if ($quoteFile && $quoteFile->getSize() > 0) {
                $location = LOCATION_UPLOAD . "/order";
                FileSystem::CreateFolder($location);

                if ($quoteFile->move("{$location}/{$item->guid}." . $quoteFile->getExtension())) {
                    $item->quoteFile = "{$item->guid}." . $quoteFile->getExtension();
                    $repo->set($item);
                }
            }

            // Update settings
            if (!$id) {
                $navItem = Arrays::first($navRepo->get(Session::get("moduleSettingsId")));
                $navItem->settings['lastNumber']++;
                $navItem->settings = $navItem->settings;
                $navRepo->set($navItem, ['settings']);
            }

            // Mail
            if (Strings::equal($item->status, "QR")) $this->mailQuote($item->id);
            else if (Strings::equal($item->status, "WA")) $this->mailAccept($item->id);
            else if (Strings::equal($item->status, "A") || Strings::equal($item->status, "D")) $this->mailStatus($item->id);
            else if (Strings::equal($item->status, "O")) $this->mailOrder($id);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De bon is opgeslagen!");
            $this->setReturn();
        }
    }

    protected function postOrderLine($view, $id = null)
    {
        $orderId = Helpers::input()->post('orderId')->getValue();
        $amount = Helpers::input()->post('amount')->getValue();
        $category = Helpers::input()->post('category')->getValue();
        $assetId = Helpers::input()->post('assetId')->getValue();
        $clarifycation = Helpers::input()->post('clarifycation')->getValue();
        $quotePrice = Helpers::input()->post('quotePrice')->getValue();
        $quoteVatIncluded = Helpers::input()->post('quoteVatIncluded')?->getValue();
        $warrenty = Helpers::input()->post('warrenty')?->getValue();

        if (!Input::check($amount) || Input::empty($amount)) $this->setValidation("amount", "Aantal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($category) || Input::empty($category)) $this->setValidation("category", "Categorie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            if (!Arrays::contains(["O"], Arrays::first(explode("-", $category)))) {
                if (!Input::check($assetId) || Input::empty($assetId)) $this->setValidation("assetId", "Toestel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            } else {
                if (!Input::check($clarifycation) || Input::empty($clarifycation)) $this->setValidation("clarifycation", "Verduidelijking moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            }

            if ($this->validationIsAllGood()) {
                $pRepo = new Order;
                $order = Arrays::firstOrNull($pRepo->get($orderId));

                $repo = new Line;
                $line = $id ? Arrays::firstOrNull($repo->get($id)) : new OrderLine;
                $line->orderId = $order->id;
                $line->amount = $amount;
                $line->category = $category;
                $line->assetId = Arrays::contains(["O"], Arrays::first(explode("-", $category))) ? "" : $assetId;
                $line->clarifycation = $clarifycation;
                $line->quotePrice = $quotePrice ?: 0;
                $line->quoteVatIncluded = Input::convertToBool($quoteVatIncluded);
                $line->warrenty = Input::convertToBool($warrenty);
                $repo->set($line);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De lijn is opgeslagen!");
            $this->setCloseModal();
            $this->setReloadTable();
        }
    }

    protected function postOrderRequestQuote($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $this->mailQuote($item->id);
            $this->setToast("Offerte aangevraagd voor bon #{$item->formatted->number}");
        }

        $this->setCloseModal();
    }


    protected function postOrderRequestAccept($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $this->mailAccept($item->id);
            $this->setToast("Goedkeuring aangevraagd voor bon #{$item->formatted->number}");
        }

        $this->setCloseModal();
    }

    protected function postOrderOrder($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $this->mailOrder($item->id);
            $this->setToast("Bestelling geplaatst voor bon #{$item->formatted->number}");
        }

        $this->setCloseModal();
    }

    protected function postSupplier($view, $id = null)
    {
        if ($id == "add") $id = null;

        $name = Helpers::input()->post('name')->getValue();
        $contactName = Helpers::input()->post('contactName')->getValue();
        $email = Helpers::input()->post('email')->getValue();
        $phone = Helpers::input()->post('phone')->getValue();

        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($email) || Input::empty($email)) $this->setValidation("email", "E-mail moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Supplier;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new OrderSupplier;
                $item->name = $name;
                $item->contactName = $contactName;
                $item->email = $email;
                $item->phone = $phone;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De leverancier is opgeslagen!");
            $this->setReturn();
        }
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
    protected function deleteOrder($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Order;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));

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
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['quote']['subject'];
        $body = $settings['mail']['template']['quote']['body'];
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
        if (General::convert($settings['mail']['template']['quote']['reply'], "boolean")) $mail->replyTo = $settings['mail']['reply'];

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
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['order']['subject'];
        $body = $settings['mail']['template']['order']['body'];
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
        if (General::convert($settings['mail']['template']['order']['reply'], "boolean")) $mail->replyTo = $settings['mail']['reply'];

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
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['accept']['subject'];
        $body = $settings['mail']['template']['accept']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['accept']['reply'], "boolean")) $mail->replyTo = $settings['mail']['reply'];

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
        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $h = $repo->get($id)[0];
        $mail = new MailMail;

        $subject = $settings['mail']['template']['status']['subject'];
        $body = $settings['mail']['template']['status']['body'];
        foreach ($h->toArray(true) as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        $mail->subject = $subject;
        $mail->body = $body;
        if (General::convert($settings['mail']['template']['status']['reply'], "boolean")) $mail->replyTo = $settings['mail']['reply'];

        $mId = $mailRepo->set($mail);

        $receiver = new MailReceiver;
        $receiver->mailId = $mId;
        $receiver->email = $h->linked->creatorUser->username;
        $receiver->name = $h->linked->creatorUser->formatted->fullName;
        $mailReceiverRepo->set($receiver);
    }
}
