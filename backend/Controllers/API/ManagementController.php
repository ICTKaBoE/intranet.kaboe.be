<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Table;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Management\CCTV;
use Database\Repository\Management\IPad;
use Database\Repository\Management\Room;
use Database\Repository\Management\Beamer;
use Database\Repository\Management\Cabinet;
use Database\Repository\Management\MSwitch;
use Database\Repository\Management\Printer;
use Database\Repository\Management\Building;
use Database\Repository\Management\Computer;
use Database\Repository\Management\Firewall;
use Database\Repository\Management\Patchpanel;
use Database\Repository\Management\AccessPoint;
use Database\Repository\Management\ComputerBattery;
use Database\Object\Management\CCTV as ManagementCCTV;
use Database\Object\Management\Room as ManagementRoom;
use Database\Repository\Management\ComputerUsageLogOn;
use Database\Repository\Management\ComputerUsageOnOff;
use Database\Object\Management\Beamer as ManagementBeamer;
use Database\Object\Management\Cabinet as ManagementCabinet;
use Database\Object\Management\MSwitch as ManagementMSwitch;
use Database\Object\Management\Printer as ManagementPrinter;
use Database\Object\Management\Building as ManagementBuilding;
use Database\Object\Management\Firewall as ManagementFirewall;
use Database\Object\Management\Patchpanel as ManagementPatchpanel;
use Database\Object\Management\AccessPoint as ManagementAccessPoint;
use Database\Object\Management\ComputerBattery as ManagementComputerBattery;
use Database\Object\Management\ComputerUsageOnOff as ManagementComputerUsageOnOff;
use Database\Repository\Helpdesk\Helpdesk;
use Database\Repository\Navigation\Setting;

class ManagementController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "management";

    // Get Functions
    protected function getLaptop($view, $id = null)
    {
        $this->getComputer($view, $id, "L");
    }
    protected function getDesktop($view, $id = null)
    {
        $this->getComputer($view, $id, "D");
    }

    protected function getComputer($view, $id = null, $type = "L")
    {
        $repo = new Computer;
        $filters = [
            'type' => $type,
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            unset($filters['type']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));

            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getComputerBattery($view, $id = null)
    {
        $computer = (new Computer)->getById($id);

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[0, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "ID",
                        "data" => "batteryId"
                    ],
                    [
                        "title" => "Design Capacity",
                        "data" => "formatted.designCapacity",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Full Charge Capactiy",
                        "data" => "formatted.fullChargeCapacity",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Capacity",
                        "data" => "formatted.capacity",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Laatste opname",
                        "data" => "formatted.lastCheck",
                        "width" => "125px"
                    ]
                ]
            );

            $items = (new ComputerBattery)->getByComputerId($computer->id);

            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getComputerUsage($view, $id = null)
    {
        $computer = (new Computer)->getById($id);

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("childRows", true);
            $this->appendToJson("defaultOrder", [[1, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "className" => "dt-control",
                        "orderable" => false,
                        "data" => null,
                        "defaultContent" => ''
                    ],
                    [
                        "title" => "Opstart",
                        "data" => "formatted.startup",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date"
                    ],
                    [
                        "title" => "Afsluit",
                        "data" => "formatted.shutdown",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date"
                    ],
                    [
                        "title" => "Duur",
                        "data" => "formatted.duration",
                        "width" => "100px"
                    ]
                ]
            );

            $items = (new ComputerUsageOnOff)->getByComputerId($computer->id);
            // $items = Arrays::filter($items, fn($i) => Strings::isNotBlank($i->shutdown));
            $logonRepo = new ComputerUsageLogOn;

            Arrays::each($items, fn($i) => $i->logon = array_reverse(Arrays::orderBy($logonRepo->getByComputerIdAndLogonBetweenStartupAndShutdown($computer->id, $i->startup, $i->shutdown), 'logon')));

            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getBuilding($view, $id = null)
    {
        $repo = new Building;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

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

    protected function getRoom($view, $id = null)
    {
        $repo = new Room;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
        ];

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

    protected function getCabinet($view, $id = null)
    {
        $repo = new Cabinet;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

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

    protected function getPatchpanel($view, $id = null)
    {
        $repo = new Patchpanel;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
            'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
        ];

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

    protected function getFirewall($view, $id = null)
    {
        $repo = new Firewall;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
            'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
        else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    protected function getSwitch($view, $id = null)
    {
        $repo = new MSwitch;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
            'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
        else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    protected function getAccessPoint($view, $id = null)
    {
        $repo = new AccessPoint;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
        else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    protected function getIpad($view, $id = null)
    {
        $repo = new IPad;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getBeamer($view, $id = null)
    {
        $repo = new Beamer;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $this->appendToJson('fields', $repo->getById($id));
        } else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    protected function getPrinter($view, $id = null)
    {
        $repo = new Printer;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            $this->appendToJson('fields', $repo->getById($id));
        } else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    protected function getPrinterMode($view, $id = null)
    {
        $items = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "printer.mode")->value;
        $items = explode(PHP_EOL, $items);
        $items = Arrays::map($items, fn($i) => ["id" => trim($i), "name" => trim($i)]);
        $this->appendToJson('items', $items);
    }

    protected function getCctv($view, $id = null)
    {
        $repo = new CCTV;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i))
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $helpdeskRepo = new Helpdesk;
            Arrays::each($items, fn($i) => $i->formatted->tickets = count($helpdeskRepo->getByMainCategoryAndAssetId($i->type, $i->id)));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
        else if (Strings::equal($view, self::VIEW_PS)) {
            $this->appendToJson("items", $repo->get(), fn($i) => $i->toArray());
        }
    }

    // Post functions
    protected function postComputerBattery($view, $id = null)
    {
        $computer = (new Computer)->getByName($id);

        if (!$computer) $this->setError("Computer not found!");

        if ($this->validationIsAllGood()) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body[0]) $body = [$body];

            $batteryRepo = new ComputerBattery;

            foreach ($body as $bat) {
                if (!$bat['id'] || is_null($bat['id'])) continue;
                $battery = $batteryRepo->getByComputerIdAndBatteryId($computer->id, $bat['id']) ?? (new ManagementComputerBattery);
                $battery->computerId = $computer->id;
                $battery->batteryId = $bat["id"];
                $battery->lastCheck = Clock::nowAsString("Y-m-d H:i:s");
                $battery->designCapacity = $bat["designCapacity"];
                $battery->fullChargeCapacity = $bat["fullChargeCapacity"];
                $battery->cycleCount = $bat["cycleCount"];

                $batteryRepo->set($battery);
            }
        }
    }

    protected function postComputerUsage($view, $id = null)
    {
        $computer = (new Computer)->getByName($id);

        if (!$computer) $this->setError("Computer not found!");

        if ($this->validationIsAllGood()) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body[0]) $body = [$body];

            $onoff = json_decode($body[0]['onoff'], true);
            // $logon = json_decode($body[0]['logon'], true);

            $onoffRepo = new ComputerUsageOnOff;
            // $logonRepo = new ComputerUsageLogOn;

            foreach ($onoff as $oo) {
                $_onoff = $onoffRepo->getByComputerIdAndStartup($computer->id, $oo['startup']) ?? (new ManagementComputerUsageOnOff);
                $_onoff->computerId = $computer->id;
                $_onoff->startup = $oo['startup'];
                $_onoff->shutdown = $oo['shutdown'] ?: null;

                $onoffRepo->set($_onoff);
            }

            // foreach ($logon as $lo) {
            //     $_logon = $logonRepo->getByComputerIdAndLogon($computer->id, $lo['logon']) ?? (new ManagementComputerUsageLogOn);
            //     $_logon->computerId = $computer->id;
            //     $_logon->username = $lo['username'] ?: null;
            //     $_logon->logon = $lo['logon'];
            //     $_logon->logoff = $lo['logoff'] ?: null;

            //     $logonRepo->set($_logon);
            // }
        }
    }

    protected function postBuilding($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Building;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementBuilding;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postRoom($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "floor" => ["default" => 0, 'type' => Input::INPUT_TYPE_INT],
            "number" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Room;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementRoom;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postCabinet($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Cabinet;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementCabinet;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postPatchpanel($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "cabinetId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "patchpoints",
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Patchpanel;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementPatchpanel;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postFirewall($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "cabinetId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "hostname" => ["mandatory" => true],
            "manufacturer" => ["mandatory" => true],
            "model" => ["mandatory" => true],
            "serialnumber" => ["mandatory" => true],
            "macaddress" => ["mandatory" => true, "type" => Input::INPUT_TYPE_MAC, "placeholder" => "__:__:__:__:__:__"],
            "ip" => ["mandatory" => true, "type" => Input::INPUT_TYPE_IP],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Firewall;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementFirewall;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postSwitch($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "cabinetId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "serialnumber" => ["mandatory" => true],
            "macaddress" => ["mandatory" => true, "type" => Input::INPUT_TYPE_MAC, "placeholder" => "__:__:__:__:__:__"],
            "ports" => ["mandatory" => true, "type" => INPUT::INPUT_TYPE_INT],
            "manufacturer" => ["mandatory" => true],
            "model" => ["mandatory" => true],
            "ip" => ["mandatory" => true, "type" => Input::INPUT_TYPE_IP],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new MSwitch;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementMSwitch;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postAccessPoint($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "serialnumber" => ["mandatory" => true],
            "macaddress" => ["mandatory" => true, "type" => Input::INPUT_TYPE_MAC, "placeholder" => "__:__:__:__:__:__"],
            "manufacturer" => ["mandatory" => true],
            "model" => ["mandatory" => true],
            "ip" => ["mandatory" => true, "type" => Input::INPUT_TYPE_IP],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new AccessPoint;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementAccessPoint;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postBeamer($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "manufacturer",
            "model",
            "serialnumber"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Beamer;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementBeamer;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postPrinter($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "mode",
            "manufacturer",
            "model",
            "serialnumber"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Printer;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementPrinter;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postCctv($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "serialnumber",
            "macaddress" => ["type" => Input::INPUT_TYPE_MAC, "placeholder" => "__:__:__:__:__:__"],
            "manufacturer",
            "model",
            "ip"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new CCTV;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ManagementCCTV;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions    
    protected function deleteBuilding($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Building;
        $roomRepo = new Room;
        $cabinetRepo = new Cabinet;
        $ppRepo = new Patchpanel;
        $fRepo = new Firewall;
        $sRepo = new MSwitch;
        $apRepo = new AccessPoint;
        $bRepo = new Beamer;
        $pRepo = new Printer;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count($roomRepo->getByBuildingId($item->id))) $attachtedTo[] = "lokalen";
            if (count($cabinetRepo->getByBuildingId($item->id))) $attachtedTo[] = "netwerkkasten";
            if (count($ppRepo->getByBuildingId($item->id))) $attachtedTo[] = "patchpanelen";
            if (count($fRepo->getByBuildingId($item->id))) $attachtedTo[] = "firewalls";
            if (count($sRepo->getByBuildingId($item->id))) $attachtedTo[] = "switches";
            if (count($apRepo->getByBuildingId($item->id))) $attachtedTo[] = "access points";
            if (count($bRepo->getByBuildingId($item->id))) $attachtedTo[] = "beamers";
            if (count($pRepo->getByBuildingId($item->id))) $attachtedTo[] = "printers";

            if (count($attachtedTo)) {
                $this->setToast("Het gebouw '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het gebouw '{$item->formatted->full}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteRoom($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Room;
        $cabinetRepo = new Cabinet;
        $ppRepo = new Patchpanel;
        $fRepo = new Firewall;
        $sRepo = new MSwitch;
        $apRepo = new AccessPoint;
        $bRepo = new Beamer;
        $pRepo = new Printer;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count($cabinetRepo->getByRoomId($item->id))) $attachtedTo[] = "netwerkkasten";
            if (count($ppRepo->getByRoomId($item->id))) $attachtedTo[] = "patchpanelen";
            if (count($fRepo->getByRoomId($item->id))) $attachtedTo[] = "firewalls";
            if (count($sRepo->getByRoomId($item->id))) $attachtedTo[] = "switches";
            if (count($apRepo->getByRoomId($item->id))) $attachtedTo[] = "access points";
            if (count($bRepo->getByRoomId($item->id))) $attachtedTo[] = "beamers";
            if (count($pRepo->getByRoomId($item->id))) $attachtedTo[] = "printers";

            if (count($attachtedTo)) {
                $this->setToast("Het lokaal '{$item->formatted->full}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het lokaal '{$item->formatted->full}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteCabinet($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Cabinet;
        $ppRepo = new Patchpanel;
        $fRepo = new Firewall;
        $sRepo = new MSwitch;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count($ppRepo->getByCabinetId($item->id))) $attachtedTo[] = "patchpanelen";
            if (count($fRepo->getByCabinetId($item->id))) $attachtedTo[] = "firewalls";
            if (count($sRepo->getByCabinetId($item->id))) $attachtedTo[] = "switches";

            if (count($attachtedTo)) {
                $this->setToast("De netwerkkast '{$item->formatted->full}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De netwerkkast '{$item->formatted->full}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deletePatchpanel($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Patchpanel;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het patchpaneel '{$item->formatted->full}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteFirewall($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Firewall;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("F", $item->id))) {
                $this->setToast("De firewall '{$item->hostname}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De firewall '{$item->hostname}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteSwitch($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new MSwitch;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("S", $item->id))) {
                $this->setToast("De switch '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De switch '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteAccessPoint($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new AccessPoint;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("A", $item->id))) {
                $this->setToast("Het access point '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het access point '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteBeamer($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Beamer;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("B", $item->id))) {
                $this->setToast("De beamer '{$item->serialnumber}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De beamer '{$item->serialnumber}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deletePrinter($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Printer;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("P", $item->id))) {
                $this->setToast("De printer '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De printer '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteCctv($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new CCTV;
        $ticketRepo = new Helpdesk;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($ticketRepo->getByMainCategoryAndAssetId("A", $item->id))) {
                $this->setToast("De CCTV-camera '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan helpdesk tickets!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De CCTV-camera '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
