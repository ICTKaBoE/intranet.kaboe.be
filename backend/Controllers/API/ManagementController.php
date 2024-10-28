<?php

namespace Controllers\API;

use Security\GUID;
use Router\Helpers;
use Security\Input;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;
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
use Database\Object\Management\ComputerUsageLogOn as ManagementComputerUsageLogOn;
use Database\Object\Management\ComputerUsageOnOff as ManagementComputerUsageOnOff;
use Helpers\General;

class ManagementController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, "computer")) $this->getComputer($view, $id);
        else if (Strings::equal($what, "computerBattery")) $this->getComputerBattery($view, $id);
        else if (Strings::equal($what, "computerUsage")) $this->getComputerUsage($view, $id);
        else if (Strings::equal($what, "laptop")) $this->getComputer($view, $id, "L");
        else if (Strings::equal($what, "desktop")) $this->getComputer($view, $id, "D");
        else if (Strings::equal($what, "building")) $this->getBuilding($view, $id);
        else if (Strings::equal($what, "room")) $this->getRoom($view, $id);
        else if (Strings::equal($what, "cabinet")) $this->getCabinet($view, $id);
        else if (Strings::equal($what, "patchpanel")) $this->getPatchpanel($view, $id);
        else if (Strings::equal($what, "firewall")) $this->getFirewall($view, $id);
        else if (Strings::equal($what, "switch")) $this->getSwitch($view, $id);
        else if (Strings::equal($what, "accesspoint")) $this->getAccessPoint($view, $id);
        else if (Strings::equal($what, "ipad")) $this->getIpad($view, $id);
        else if (Strings::equal($what, "beamer")) $this->getBeamer($view, $id);
        else if (Strings::equal($what, "printer")) $this->getPrinter($view, $id);
        else if (Strings::equal($what, "printerMode")) $this->getPrinterMode($view, $id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($view, $what, $id = null)
    {
        if (Strings::equal($what, "computerBattery")) $this->postComputerBattery($id);
        else if (Strings::equal($what, "computerUsage")) $this->postComputerUsage($id);
        else if (Strings::equal($what, "building")) $this->postBuilding($id);
        else if (Strings::equal($what, "room")) $this->postRoom($id);
        else if (Strings::equal($what, "cabinet")) $this->postCabinet($id);
        else if (Strings::equal($what, "patchpanel")) $this->postPatchpanel($id);
        else if (Strings::equal($what, "firewall")) $this->postFirewall($id);
        else if (Strings::equal($what, "switch")) $this->postSwitch($id);
        else if (Strings::equal($what, "accesspoint")) $this->postAccessPoint($id);
        else if (Strings::equal($what, "beamer")) $this->postBeamer($id);
        else if (Strings::equal($what, "printer")) $this->postPrinter($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function delete($view, $what, $id = null)
    {
        if (Strings::equal($what, "building")) $this->deleteBuilding($id);
        else if (Strings::equal($what, "room")) $this->deleteRoom($id);
        else if (Strings::equal($what, "cabinet")) $this->deleteCabinet($id);
        else if (Strings::equal($what, "patchpanel")) $this->deletePatchpanel($id);
        else if (Strings::equal($what, "firewall")) $this->deleteFirewall($id);
        else if (Strings::equal($what, "switch")) $this->deleteSwitch($id);
        else if (Strings::equal($what, "accesspoint")) $this->deleteAccessPoint($id);
        else if (Strings::equal($what, "beamer")) $this->deleteBeamer($id);
        else if (Strings::equal($what, "printer")) $this->deletePrinter($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
        }
        $this->handle();
    }

    // Get Functions
    private function getComputer($view, $id = null, $type = "L")
    {
        $repo = new Computer;
        $filters = [
            'type' => $type,
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            unset($filters['type']);

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"]]);
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
                        "title" => "<i class='ti ti-devices-2'></i>",
                        "data" => "formatted.icon.type",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "10px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Merk/Model",
                        "data" => "formatted.manModel",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Operating System",
                        "data" => "formatted.os",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Intune Order ID",
                        "data" => "orderId",
                        "width" => "275px"
                    ],
                    [
                        "title" => "Intune Enrollment Profile",
                        "data" => "enrollmentProfileName",
                        "width" => "300px"
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
        }
    }

    private function getComputerBattery($view, $id = null)
    {
        $computer = Arrays::first((new Computer)->get($id));

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", false);
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

    private function getComputerUsage($view, $id = null)
    {
        $computer = Arrays::first((new Computer)->get($id));

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", false);
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
            $logonRepo = new ComputerUsageLogOn;

            Arrays::each($items, fn($i) => $i->logon = array_reverse(Arrays::orderBy($logonRepo->getByComputerIdAndLogonBetweenStartupAndShutdown($computer->id, $i->startup, $i->shutdown), 'logon')));

            $this->appendToJson("rows", array_values($items));
        }
    }

    private function getBuilding($view, $id = null)
    {
        $repo = new Building;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Volledige weergave",
                        "data" => "formatted.full"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getRoom($view, $id = null)
    {
        $repo = new Room;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Verdiep",
                        "data" => "floor",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Nummer",
                        "data" => "number",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Volledige weergave",
                        "data" => "formatted.full"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getCabinet($view, $id = null)
    {
        $repo = new Cabinet;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Volledige weergave",
                        "data" => "formatted.full"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getPatchpanel($view, $id = null)
    {
        $repo = new Patchpanel;

        if (Strings::equal($view, "table")) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
                'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
                'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
                'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"], [5, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Netwerkkast",
                        "data" => "linked.cabinet.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Aantal patchpunten",
                        "data" => "patchpoints",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Volledige weergave",
                        "data" => "formatted.full"
                    ]
                ]
            );

            $items = $repo->get();

            foreach ($filters as $key => $value) {
                if (!$value || empty($value)) continue;

                if (is_array($value)) $items = Arrays::filter($items, fn($i) => Arrays::contains($value, $i->$key));
                else $items = Arrays::filter($items, fn($i) => Strings::equal($i->$key, $value));
            }

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, "select")) {
            $cabinetId = Helpers::url()->getParam('cabinetId');
            $items = $repo->get($id);

            if ($cabinetId) $items = Arrays::filter($items, fn($i) => Strings::equal($i->cabinetId, $cabinetId));

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getFirewall($view, $id = null)
    {
        $repo = new Firewall;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
            'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"], [5, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Netwerkkast",
                        "data" => "linked.cabinet.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Hostnaam",
                        "data" => "hostname"
                    ],
                    [
                        "title" => "Merk",
                        "data" => "manufacturer",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber",
                        "width" => "200px"
                    ],
                    [
                        "title" => "MAC Adres",
                        "data" => "formatted.macaddress",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Beheerlink",
                        "data" => "formatted.link",
                        "width" => "150px"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getSwitch($view, $id = null)
    {
        $repo = new MSwitch;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
            'cabinetId' => Arrays::filter(explode(";", Helpers::url()->getParam('cabinetId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"], [5, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Netwerkkast",
                        "data" => "linked.cabinet.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Merk",
                        "data" => "manufacturer",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber",
                        "width" => "200px"
                    ],
                    [
                        "title" => "MAC Adres",
                        "data" => "formatted.macaddress",
                        "width" => "150px"
                    ],
                    [
                        "title" => "# Poorten",
                        "data" => "ports",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Beheerslink",
                        "data" => "formatted.ip",
                        "width" => "150px"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getAccessPoint($view, $id = null)
    {
        $repo = new AccessPoint;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Merk",
                        "data" => "manufacturer",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber",
                        "width" => "200px"
                    ],
                    [
                        "title" => "MAC Adres",
                        "data" => "formatted.macaddress",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Beheerslink",
                        "data" => "formatted.ip",
                        "width" => "150px"
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
        } else if (Strings::equal($view, 'form')) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    private function getIpad($view, $id = null)
    {
        $repo = new IPad;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[0, "asc"], [1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "250px"
                    ],
                    [
                        "title" => "Operating System",
                        "data" => "formatted.os",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Batterij",
                        "data" => "formatted.badge.battery",
                        "orderable" => false,
                        "width" => "75px"
                    ],
                    [
                        "title" => "Opslag %",
                        "data" => "formatted.badge.capacity",
                        "orderable" => false,
                        "width" => "75px"
                    ],
                    [
                        "title" => "Opslag",
                        "data" => "formatted.capacity",
                        "orderable" => false,
                        "width" => "200px"
                    ],
                    [
                        "title" => "JAMF ID",
                        "data" => "jamfId",
                        "width" => "350px"
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
        }
    }

    private function getBeamer($view, $id = null)
    {
        $repo = new Beamer;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Merk",
                        "data" => "manufacturer",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber"
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
        } else if (Strings::equal($view, "form")) {
            $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
        }
    }

    private function getPrinter($view, $id = null)
    {
        $repo = new Printer;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'buildingId' => Arrays::filter(explode(";", Helpers::url()->getParam('buildingId')), fn($i) => Strings::isNotBlank($i)),
            'roomId' => Arrays::filter(explode(";", Helpers::url()->getParam('roomId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, "table")) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"], [4, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Gebouw",
                        "data" => "linked.building.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Lokaal",
                        "data" => "linked.room.formatted.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Mode",
                        "data" => "formatted.mode",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Merk",
                        "data" => "manufacturer",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Model",
                        "data" => "model",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Serienummer",
                        "data" => "serialnumber",
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
        } else if (Strings::equal($view, "form")) {
            $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
        }
    }

    private function getPrinterMode($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $statuses = $settings['printer']['mode'];

        if (Strings::equal($view, "select")) {
            $_statuses = [];

            foreach ($statuses as $k => $v) $_statuses[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_statuses);
        }
    }

    // Post functions
    private function postComputerBattery($id = null)
    {
        $computer = (new Computer)->getByName($id);

        if (!$computer) $this->setError("Computer not found!");

        if ($this->validationIsAllGood()) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body[0]) $body = [$body];

            $batteryRepo = new ComputerBattery;

            foreach ($body as $bat) {
                if (!$bat['id']) continue;
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

    private function postComputerUsage($id = null)
    {
        $computer = (new Computer)->getByName($id);

        if (!$computer) $this->setError("Computer not found!");

        if ($this->validationIsAllGood()) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body[0]) $body = [$body];

            $onoff = json_decode($body[0]['onoff'], true);
            $logon = json_decode($body[0]['logon'], true);

            $onoffRepo = new ComputerUsageOnOff;
            $logonRepo = new ComputerUsageLogOn;

            foreach ($onoff as $oo) {
                $_onoff = $onoffRepo->getByComputerIdAndStartup($computer->id, $oo['startup']) ?? (new ManagementComputerUsageOnOff);
                $_onoff->computerId = $computer->id;
                $_onoff->startup = $oo['startup'];
                $_onoff->shutdown = $oo['shutdown'];

                $onoffRepo->set($_onoff);
            }

            foreach ($logon as $lo) {
                $_logon = $logonRepo->getByComputerIdAndLogon($computer->id, $lo['logon']) ?? (new ManagementComputerUsageLogOn);
                $_logon->computerId = $computer->id;
                $_logon->username = $lo['username'];
                $_logon->logon = $lo['logon'];
                $_logon->logoff = $lo['logoff'];

                $logonRepo->set($_logon);
            }
        }
    }

    private function postBuilding($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet aangeduid zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Building;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementBuilding;
                $item->schoolId = $schoolId;
                $item->name = $name;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het gebouw is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postRoom($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $floor = Helpers::input()->post('floor')->getValue();
        $number = Helpers::input()->post('number')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (Input::empty($floor)) $this->setValidation("floor", "Verdiep moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (Input::empty($number)) $this->setValidation("number", "Nummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Room;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementRoom;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->floor = $floor;
                $item->number = $number;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het lokaal is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postCabinet($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $name = Helpers::input()->post('name')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Cabinet;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementCabinet;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->name = $name;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De netwerkkast is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postPatchpanel($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $cabinetId = Helpers::input()->post('cabinetId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $patchpoints = Helpers::input()->post('patchpoints')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($cabinetId) || Input::empty($cabinetId)) $this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Patchpanel;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementPatchpanel;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->cabinetId = $cabinetId;
                $item->name = $name;
                $item->patchpoints = $patchpoints;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het patchpaneel is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postFirewall($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $cabinetId = Helpers::input()->post('cabinetId')->getValue();
        $hostname = Helpers::input()->post('hostname')->getValue();
        $manufacturer = Helpers::input()->post('manufacturer')->getValue();
        $model = Helpers::input()->post('model')->getValue();
        $serialnumber = Helpers::input()->post('serialnumber')->getValue();
        $macaddress = Helpers::input()->post('macaddress')->getValue();
        $link = Helpers::input()->post('link')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($cabinetId) || Input::empty($cabinetId)) $this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($hostname) || Input::empty($hostname)) $this->setValidation("hostname", "Hostnaam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($manufacturer) || Input::empty($manufacturer)) $this->setValidation("manufacturer", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($model) || Input::empty($model)) $this->setValidation("model", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($macaddress) || Input::empty($macaddress)) $this->setValidation("macaddress", "MAC Adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($link) || Input::empty($link)) $this->setValidation("link", "Beheerslink moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Firewall;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementFirewall;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->cabinetId = $cabinetId;
                $item->hostname = $hostname;
                $item->manufacturer = $manufacturer;
                $item->model = $model;
                $item->serialnumber = $serialnumber;
                $item->macaddress = $macaddress;
                $item->link = $link;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De firewall is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postSwitch($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $cabinetId = Helpers::input()->post('cabinetId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $serialnumber = Helpers::input()->post('serialnumber')->getValue();
        $macaddress = Helpers::input()->post('macaddress')->getValue();
        $ports = Helpers::input()->post('ports')->getValue();
        $manufacturer = Helpers::input()->post('manufacturer')->getValue();
        $model = Helpers::input()->post('model')->getValue();
        $ip = Helpers::input()->post('ip')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($cabinetId) || Input::empty($cabinetId)) $this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($macaddress) || Input::empty($macaddress)) $this->setValidation("macaddress", "MAC Adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($ports) || Input::empty($ports)) $this->setValidation("ports", "# Poorten moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($manufacturer) || Input::empty($manufacturer)) $this->setValidation("manufacturer", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($model) || Input::empty($model)) $this->setValidation("model", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($ip) || Input::empty($ip)) $this->setValidation("ip", "IP Adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new MSwitch;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementMSwitch;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->cabinetId = $cabinetId;
                $item->name = $name;
                $item->serialnumber = $serialnumber;
                $item->macaddress = $macaddress;
                $item->ports = $ports;
                $item->manufacturer = $manufacturer;
                $item->model = $model;
                $item->ip = $ip;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De switch is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postAccessPoint($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $serialnumber = Helpers::input()->post('serialnumber')->getValue();
        $macaddress = Helpers::input()->post('macaddress')->getValue();
        $manufacturer = Helpers::input()->post('manufacturer')->getValue();
        $model = Helpers::input()->post('model')->getValue();
        $ip = Helpers::input()->post('ip')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new AccessPoint;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementAccessPoint;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->name = $name;
                $item->serialnumber = $serialnumber;
                $item->macaddress = $macaddress;
                $item->manufacturer = $manufacturer;
                $item->model = $model;
                $item->ip = $ip;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het access point is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postBeamer($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $manufacturer = Helpers::input()->post('manufacturer')->getValue();
        $model = Helpers::input()->post('model')->getValue();
        $serialnumber = Helpers::input()->post('serialnumber')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Beamer;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementBeamer;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->manufacturer = $manufacturer;
                $item->model = $model;
                $item->serialnumber = $serialnumber;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het access point is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postPrinter($id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $buildingId = Helpers::input()->post('buildingId')->getValue();
        $roomId = Helpers::input()->post('roomId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $mode = Helpers::input()->post('mode')->getValue();
        $manufacturer = Helpers::input()->post('manufacturer')->getValue();
        $model = Helpers::input()->post('model')->getValue();
        $serialnumber = Helpers::input()->post('serialnumber')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($buildingId) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($roomId) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($mode) || Input::empty($mode)) $this->setValidation("mode", "Modus moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Printer;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ManagementPrinter;
                $item->schoolId = $schoolId;
                $item->buildingId = $buildingId;
                $item->roomId = $roomId;
                $item->name = $name;
                $item->mode = $mode;
                $item->manufacturer = $manufacturer;
                $item->model = $model;
                $item->serialnumber = $serialnumber;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De printer is opgeslagen!");
            $this->setReturn();
        }
    }

    // Delete functions    
    private function deleteBuilding($id = null)
    {
        $id = explode(";", $id);
        $repo = new Building;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het gebouw '{$item->formatted->full}' is verwijderd!");
        }
    }

    private function deleteRoom($id = null)
    {
        $id = explode(";", $id);
        $repo = new Room;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het lokaal '{$item->formatted->full}' is verwijderd!");
        }
    }

    private function deleteCabinet($id = null)
    {
        $id = explode(";", $id);
        $repo = new Cabinet;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De netwerkkast '{$item->formatted->full}' is verwijderd!");
        }
    }

    private function deletePatchpanel($id = null)
    {
        $id = explode(";", $id);
        $repo = new Patchpanel;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het patchpaneel '{$item->formatted->full}' is verwijderd!");
        }
    }

    private function deleteFirewall($id = null)
    {
        $id = explode(";", $id);
        $repo = new Firewall;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De firewall '{$item->hostname}' is verwijderd!");
        }
    }

    private function deleteSwitch($id = null)
    {
        $id = explode(";", $id);
        $repo = new MSwitch;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De switch '{$item->name}' is verwijderd!");
        }
    }

    private function deleteAccessPoint($id = null)
    {
        $id = explode(";", $id);
        $repo = new AccessPoint;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het access point '{$item->name}' is verwijderd!");
        }
    }

    private function deleteBeamer($id = null)
    {
        $id = explode(";", $id);
        $repo = new Beamer;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De beamer '{$item->serialnumber}' is verwijderd!");
        }
    }

    private function deletePrinter($id = null)
    {
        $id = explode(";", $id);
        $repo = new Printer;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De printer '{$item->name}' is verwijderd!");
        }
    }
}
