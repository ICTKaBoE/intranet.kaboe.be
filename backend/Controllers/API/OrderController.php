<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;
use Database\Repository\Order\Supplier;
use Database\Repository\Management\Building;
use Database\Repository\Management\Computer;
use Database\Repository\Management\ComputerBattery;
use Database\Object\Order\Supplier as OrderSupplier;
use Database\Object\Management\ComputerBattery as ManagementComputerBattery;
use Database\Object\Order\Purchase as OrderPurchase;
use Database\Repository\Order\Purchase;
use Database\Repository\Order\PurchaseLine;
use Security\User;

class OrderController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, "status")) $this->getStatus($view, $id);
        else if (Strings::equal($what, "purchase")) $this->getPurchase($view, $id);
        else if (Strings::equal($what, "purchaseLine")) $this->getPurchaseLine($view, $id);
        else if (Strings::equal($what, "supplier")) $this->getSupplier($view, $id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($view, $what, $id = null)
    {
        if (Strings::equal($what, "purchase")) $this->postPurchase($id);
        else if (Strings::equal($what, "supplier")) $this->postSupplier($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function delete($view, $what, $id = null)
    {
        if (Strings::equal($what, "purchase")) $this->deletePurchase($id);
        if (Strings::equal($what, "supplier")) $this->deleteSupplier($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
        }
        $this->handle();
    }

    // Get Functions
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

    private function getPurchase($view, $id = null)
    {
        $repo = new Purchase;
        $filters = [];

        if (Strings::equal($view, "table")) {
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
        } else if (Strings::equal($view, "select")) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getPurchaseLine($view, $id = null)
    {
        $repo = new PurchaseLine;
        $filters = [];

        if (Strings::equal($view, "table")) {
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
                        "title" => "<i class='ti ti-circle-check' title='Goedgekeurd?'></i>",
                        "data" => "formatted.icon.accepted",
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
                        "data" => "formatted.subject"
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
        } else if (Strings::equal($view, "select")) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getSupplier($view, $id = null)
    {
        $repo = new Supplier;
        $filters = [];

        if (Strings::equal($view, "table")) {
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
        } else if (Strings::equal($view, "select")) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    // Post functions
    private function postPurchase($id = null)
    {
        if ($id == "add") $id = null;

        $navRepo = new Navigation;
        $settings = Arrays::first($navRepo->get(Session::get("moduleSettingsId")))->settings;

        $status = Helpers::input()->post('status')->getValue();
        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $acceptorUserId = Helpers::input()->post('acceptorUserId')->getValue();
        $supplierId = Helpers::input()->post('supplierId')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($acceptorUserId) || Input::empty($acceptorUserId)) $this->setValidation("acceptorUserId", "Goed te keuren door moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Purchase;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new OrderPurchase;
                if (!$item->number) $item->number = $settings['lastNumber'] + 1;
                $item->status = $status;
                $item->schoolId = $schoolId;
                $item->acceptorUserId = $acceptorUserId;
                $item->supplierId = $supplierId;
                $item->creatorUserId = User::getLoggedInUser()->id;
                $repo->set($item);
            }

            // Update settings
            if (!$id) {
                $navItem = Arrays::first($navRepo->get(Session::get("moduleSettingsId")));
                $navItem->settings['lastNumber']++;
                $navItem->settings = json_encode($navItem->settings);
                $navRepo->set($navItem, ['settings']);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De bon is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postSupplier($id = null)
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

    // Delete functions     
    private function deletePurchase($id = null)
    {
        $id = explode(";", $id);
        $repo = new Purchase;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De bon '{$item->formatted->number}' is verwijderd!");
        }
    }

    private function deleteSupplier($id = null)
    {
        $id = explode(";", $id);
        $repo = new Supplier;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De leverancier '{$item->name}' is verwijderd!");
        }
    }
}
