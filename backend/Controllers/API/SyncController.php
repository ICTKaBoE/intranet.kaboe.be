<?php

namespace Controllers\API;

use finfo;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Management\Management;
use O365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Informat\Repository\Staff;
use Database\Repository\Module;
use Database\Repository\LocalUser;
use Database\Repository\SyncStaff;
use Database\Repository\UserSecurity;
use Database\Repository\SchoolInstitute;
use Database\Repository\ManagementComputer;
use Database\Object\LocalUser as ObjectLocalUser;
use Database\Object\ManagementComputer as ObjectManagementComputer;
use Database\Object\SyncStaff as ObjectSyncStaff;
use Database\Object\UserSecurity as ObjectUserSecurity;

class SyncController extends ApiController
{
	public function adUser()
	{
		set_time_limit(0);

		$groupRepo = new Group;
		$localUserRepo = new LocalUser;
		$userSecurityRepo = new UserSecurity;
		$group = $groupRepo->getByDisplayName('secur.intranet.kaboe.be')->doRequest()[0];
		$modules = (new Module)->getWhereAssignUserRights();

		$allMembers = $groupRepo->getMembersByGroupId($group->getId())->doRequestAllPages(\Microsoft\Graph\Model\User::class, 300);
		$allMembers = Arrays::filter($allMembers, fn ($m) => Strings::equalsIgnoreCase($m->getODataType(), "#microsoft.graph.user"));

		foreach ($allMembers as $member) {
			try {
				$user = $localUserRepo->getByO365Id($member->getId()) ?? new ObjectLocalUser;

				$user->o365Id = $member->getId();
				$user->name = $member->getSurname();
				$user->firstName = $member->getGivenName();
				$user->username = $member->getMail();
				$user->jobTitle = $member->getJobTitle();
				$user->companyName = $member->getCompanyName();
				$user->active = $member->getAccountEnabled();

				$userId = $localUserRepo->set($user);

				if (is_null($user->id)) $user = $localUserRepo->get($userId);
				else $userId = $user->id;

				foreach ($modules as $module) {
					$rights = str_split($module->defaultRights);
					if (Strings::equal($rights[0], 0)) continue;

					$userSecurity = $userSecurityRepo->getByUserAndModule($userId, $module->id) ?? new ObjectUserSecurity([
						'moduleId' => $module->id,
						'userId' => $userId,
						'view' => $rights[0],
						'edit' => $rights[1],
						'export' => $rights[2],
						'changeSettings' => $rights[3]
					]);

					try {
						$userSecurityRepo->set($userSecurity);
					} catch (\Exception $e) {
						die(var_dump("Security update: " . $e->getMessage()));
					}
				}
			} catch (\Exception $e) {
				die(var_dump("Member update: " . $e->getMessage()));
			}
		}
	}

	public function informatStaff()
	{
		$informatStaffRepo = new Staff;
		$instituteRepo = new SchoolInstitute;
		$syncStaffRepo = new SyncStaff;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncStaffRepo->db->beginTransaction();

				$informatStaffRepo->setInstituteNumber($institute->instituteNumber);
				$staff = $informatStaffRepo->get();

				foreach ($staff as $s) {
					$syncStaff = $syncStaffRepo->getByInformatUID($s->p_persoon) ?? new ObjectSyncStaff;
					$syncStaff->informatUID = $s->p_persoon;
					$syncStaff->masterNumber = $s->Stamnummer;
					$syncStaff->name = $s->Naam;
					$syncStaff->firstName = $s->Voornaam;
					$syncStaff->birthPlace = $s->Geboorteplaats;
					$syncStaff->birthDate = Clock::at($s->Geboortedatum)->format("Y-m-d");
					$syncStaff->sex = $s->GeslachtForDB;
					$syncStaff->insz = $s->Rijksregnr;
					$syncStaff->diploma = $s->Diploma;
					$syncStaff->homePhone = $s->Thuistelefoon;
					$syncStaff->mobilePhone = $s->Gsm;
					$syncStaff->privateEmail = $s->Prive_email;
					$syncStaff->schoolEmail = $s->School_email;
					$syncStaff->addressStreet = $s->Straat;
					$syncStaff->addressNumber = $s->Nr;
					$syncStaff->addressBus = $s->Bus;
					$syncStaff->addressZipcode = $s->Dlpostnr;
					$syncStaff->addressCity = $s->Dlgem;
					$syncStaff->addressCountry = $s->Landcode;
					$syncStaff->bankAccount = $s->Iban;
					$syncStaff->bankId = $s->Bic;
					$syncStaff->active = $s->ActiefForDB;

					$syncStaffRepo->set($syncStaff);
				}

				$syncStaffRepo->db->commit();
			} catch (\Exception $e) {
				$syncStaffRepo->db->rollback();
			}
		}

		$this->handle();
	}

	public function managementStaff()
	{
		$syncStaffRepo = new SyncStaff;
		$management = new Management;

		foreach ($syncStaffRepo->get() as $syncStaff) {
			try {
				$management->addOrUpdateUser($syncStaff->schoolEmail, MANAGEMENT_DEFAULT_PASS, $syncStaff->name, $syncStaff->firstName, $syncStaff->informatUID, active: $syncStaff->active);
			} catch (\Exception $e) {
				$this->appendToJson("error", $e->getMessage());
			}
		}

		$this->handle();
	}

	public function adManagementComputer()
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
		$delete = false;

		if (!$delete) {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new ManagementComputer;
			$computer = Arrays::firstOrNull($repo->getBySchoolAndName($schoolId, $name)) ?? new ObjectManagementComputer;

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

			$repo->set($computer);
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else $this->appendToJson("message", "Success!");
		$this->handle();
	}
}
