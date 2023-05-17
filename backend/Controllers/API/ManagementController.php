<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\ManagementAccesspoint as ObjectManagementAccesspoint;
use Database\Object\ManagementBeamer as ObjectManagementBeamer;
use Database\Repository\UserProfile;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementVlan;
use Database\Repository\ManagementCabinet;
use Database\Repository\ManagementBuilding;
use Database\Repository\ManagementFirewall;
use Database\Repository\ManagementPatchpanel;
use Database\Object\ManagementRoom as ObjectManagementRoom;
use Database\Object\ManagementCabinet as ObjectManagementCabinet;
use Database\Object\ManagementBuilding as ObjectManagementBuilding;
use Database\Object\ManagementComputer as ObjectManagementComputer;
use Database\Object\ManagementFirewall as ObjectManagementFirewall;
use Database\Object\ManagementPatchpanel as ObjectManagementPatchpanel;
use Database\Object\ManagementPrinter as ObjectManagementPrinter;
use Database\Object\ManagementSwitch as ObjectManagementSwitch;
use Database\Repository\ManagementAccesspoint;
use Database\Repository\ManagementBeamer;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementPrinter;
use Database\Repository\ManagementSwitch;

class ManagementController extends ApiController
{
	public function building($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementBuilding;
			$building = is_null($id) ? new ObjectManagementBuilding : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een gebouw met deze naam!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$building->schoolId = $schoolId;
					$building->name = $name;
				} else $building->deleted = true;

				$repo->set($building);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/building");
		$this->handle();
	}

	public function room($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$floor = Helpers::input()->post('floor')?->getValue();
		$number = Helpers::input()->post('number')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementRoom;
			$room = is_null($id) ? new ObjectManagementRoom : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $floor, $number, $id))) {
				$this->setValidation("floor", "Er bestaat al een lokaal met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$room->schoolId = $schoolId;
					$room->buildingId = $buildingId;
					$room->floor = $floor;
					$room->number = $number;
				} else $room->deleted = true;

				$repo->set($room);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/rooms");
		$this->handle();
	}

	public function cabinet($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($roomId)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementCabinet;
			$cabinet = is_null($id) ? new ObjectManagementCabinet : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een netwerkkast met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$cabinet->schoolId = $schoolId;
					$cabinet->buildingId = $buildingId;
					$cabinet->roomId = $roomId;
					$cabinet->name = $name;
				} else $cabinet->deleted = true;

				$repo->set($cabinet);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/cabinet");
		$this->handle();
	}

	public function patchpanel($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$ports = Helpers::input()->post('ports')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($cabinetId, Input::INPUT_TYPE_INT) || Input::empty($cabinetId)) $this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($roomId)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($ports, Input::INPUT_TYPE_INT) || Input::empty($ports)) $this->setValidation("ports", "Aantal patchpunten moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementPatchpanel;
			$patchpanel = is_null($id) ? new ObjectManagementPatchpanel : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een patchpaneel met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$patchpanel->schoolId = $schoolId;
					$patchpanel->buildingId = $buildingId;
					$patchpanel->roomId = $roomId;
					$patchpanel->cabinetId = $cabinetId;
					$patchpanel->name = $name;
					$patchpanel->ports = $ports;
				} else $patchpanel->deleted = true;

				$repo->set($patchpanel);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/patchpanel");
		$this->handle();
	}

	public function firewall($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$hostname = Helpers::input()->post('hostname')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$model = Helpers::input()->post('model')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$firmware = Helpers::input()->post('firmware')?->getValue();
		$interface = Helpers::input()->post('interface')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($cabinetId, Input::INPUT_TYPE_INT) || Input::empty($cabinetId)) $this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementFirewall;
			$firewall = is_null($id) ? new ObjectManagementFirewall : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $hostname, $id))) {
				$this->setValidation("hostname", "Er bestaat al een netwerkkast met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$firewall->schoolId = $schoolId;
					$firewall->buildingId = $buildingId;
					$firewall->roomId = $roomId;
					$firewall->cabinetId = $cabinetId;
					$firewall->hostname = $hostname;
					$firewall->brand = $brand;
					$firewall->model = $model;
					$firewall->serialnumber = $serialnumber;
					$firewall->firmware = $firmware;
					$firewall->macaddress = $macaddress;
					$firewall->interface = rtrim($interface, ":");
					$firewall->username = $username;
					$firewall->password = $password;
				} else $firewall->deleted = true;

				$repo->set($firewall);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/firewall");
		$this->handle();
	}

	public function switch($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$description = Helpers::input()->post('description')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$ip = Helpers::input()->post('ip')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$ports = Helpers::input()->post('ports')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($ports, Input::INPUT_TYPE_INT) || Input::empty($ports)) $this->setValidation("ports", "Aantal poorten moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($brand) || Input::empty($brand)) $this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($macaddress) || Input::empty($macaddress)) $this->setValidation("macaddress", "MAC adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($ip) || Input::empty($ip)) $this->setValidation("ip", "IP adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($password) || Input::empty($password)) $this->setValidation("password", "Wachtwoord moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementSwitch;
			$switch = is_null($id) ? new ObjectManagementSwitch : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een switch met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$switch->schoolId = $schoolId;
					$switch->buildingId = $buildingId;
					$switch->roomId = $roomId;
					$switch->cabinetId = $cabinetId;
					$switch->name = $name;
					$switch->brand = $brand;
					$switch->type = $type;
					$switch->description = $description;
					$switch->serialnumber = $serialnumber;
					$switch->macaddress = $macaddress;
					$switch->ip = rtrim($ip, ":");
					$switch->username = $username;
					$switch->password = $password;
					$switch->ports = $ports;
				} else $switch->deleted = true;

				$repo->set($switch);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/switch");
		$this->handle();
	}

	public function accesspoint($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$model = Helpers::input()->post('model')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$firmware = Helpers::input()->post('firmware')?->getValue();
		$ip = Helpers::input()->post('ip')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($brand) || Input::empty($brand)) $this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($model) || Input::empty($model)) $this->setValidation("model", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($macaddress) || Input::empty($macaddress) || Strings::equal($macaddress, "__:__:__:__:__:__")) $this->setValidation("macaddress", "MAC adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($ip) || Input::empty($ip) || Strings::equal($ip, "_._._._:")) $this->setValidation("ip", "IP adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($password) || Input::empty($password)) $this->setValidation("password", "Wachtwoord moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementAccesspoint;
			$ap = is_null($id) ? new ObjectManagementAccesspoint : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een accesspoint met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$ap->schoolId = $schoolId;
					$ap->buildingId = $buildingId;
					$ap->roomId = $roomId;
					$ap->name = $name;
					$ap->brand = $brand;
					$ap->model = $model;
					$ap->serialnumber = $serialnumber;
					$ap->macaddress = $macaddress;
					$ap->firmware = $firmware;
					$ap->ip = rtrim($ip, ":");
					$ap->username = $username;
					$ap->password = $password;
				} else $ap->deleted = true;

				$repo->set($ap);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/accesspoint");
		$this->handle();
	}

	public function computer($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$osType = Helpers::input()->post('osType')?->getValue();
		$osNumber = Helpers::input()->post('osNumber')?->getValue();
		$osBuild = Helpers::input()->post('osBuild')?->getValue();
		$osArchitecture = Helpers::input()->post('osArchitecture')?->getValue();
		$systemManufacturer = Helpers::input()->post('systemManufacturer')?->getValue();
		$systemModel = Helpers::input()->post('systemModel')?->getValue();
		$systemMemory = Helpers::input()->post('systemMemory')?->getValue();
		$systemProcessor = Helpers::input()->post('systemProcessor')?->getValue();
		$systemSerialnumber = Helpers::input()->post('systemSerialnumber')?->getValue();
		$systemBiosManufacturer = Helpers::input()->post('systemBiosManufacturer')?->getValue();
		$systemBiosVersion = Helpers::input()->post('systemBiosVersion')?->getValue();
		$systemDrive = Helpers::input()->post('systemDrive')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($systemManufacturer) || Input::empty($systemManufacturer)) $this->setValidation("systemManufacturer", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($systemModel) || Input::empty($systemModel)) $this->setValidation("systemModel", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementComputer;
			$computer = is_null($id) ? new ObjectManagementComputer : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
				$this->setValidation("name", "Er bestaat al een computer met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$computer->schoolId = $schoolId;
					$computer->buildingId = $buildingId;
					$computer->roomId = $roomId;
					$computer->type = $type;
					$computer->name = $name;
					$computer->osType = $osType;
					$computer->osNumber = $osNumber;
					$computer->osBuild = $osBuild;
					$computer->osArchitecture = $osArchitecture;
					$computer->systemManufacturer = $systemManufacturer;
					$computer->systemModel = $systemModel;
					$computer->systemMemory = $systemMemory;
					$computer->systemProcessor = $systemProcessor;
					$computer->systemSerialnumber = $systemSerialnumber;
					$computer->systemBiosManufacturer = $systemBiosManufacturer;
					$computer->systemBiosVersion = $systemBiosVersion;
					$computer->systemDrive = $systemDrive;
				} else $computer->deleted = true;

				$repo->set($computer);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/computer");
		$this->handle();
	}

	public function beamer($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($brand) || Input::empty($brand)) $this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementBeamer;
			$beamer = is_null($id) ? new ObjectManagementBeamer : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $serialnumber, $id))) {
				$this->setValidation("brand", "Er bestaat al een beamer met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$beamer->schoolId = $schoolId;
					$beamer->buildingId = $buildingId;
					$beamer->roomId = $roomId;
					$beamer->brand = $brand;
					$beamer->type = $type;
					$beamer->serialnumber = $serialnumber;
				} else $beamer->deleted = true;

				$repo->set($beamer);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/beamer");
		$this->handle();
	}

	public function printer($prefix, $method, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$colormode = Helpers::input()->post('colormode')?->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) $this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) $this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($brand) || Input::empty($brand)) $this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) $this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($colormode) || Input::empty($colormode)) $this->setValidation("colormode", "Kleurmodus moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementPrinter;
			$printer = is_null($id) ? new ObjectManagementPrinter : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($schoolId, $serialnumber, $id))) {
				$this->setValidation("brand", "Er bestaat al een beamer met deze combinatie!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$printer->schoolId = $schoolId;
					$printer->buildingId = $buildingId;
					$printer->roomId = $roomId;
					$printer->name = $name;
					$printer->brand = $brand;
					$printer->type = $type;
					$printer->serialnumber = $serialnumber;
					$printer->colormode = $colormode;
				} else $printer->deleted = true;

				$repo->set($printer);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/management/printer");
		$this->handle();
	}
}
