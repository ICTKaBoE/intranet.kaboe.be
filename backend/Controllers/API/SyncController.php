<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use O365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\InformatStaff as ObjectInformatStaff;
use Database\Object\InformatStaffAssignment as ObjectInformatStaffAssignment;
use Database\Object\InformatStudent as ObjectInformatStudent;
use Database\Object\InformatStudentExtra as ObjectInformatStudentExtra;
use Database\Object\InformatStudentSubgroup as ObjectInformatStudentSubgroup;
use Database\Object\InformatStudentSubscription as ObjectInformatStudentSubscription;
use Informat\Repository\Staff;
use Database\Repository\Module;
use Database\Repository\LocalUser;
use Database\Repository\InformatStaff;
use Database\Repository\UserSecurity;
use Database\Repository\SchoolInstitute;
use Database\Repository\ManagementComputer;
use Database\Object\LocalUser as ObjectLocalUser;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Object\ManagementComputer as ObjectManagementComputer;
use Database\Object\SyncStudent as ObjectSyncStudent;
use Database\Object\SyncTeacher as ObjectSyncTeacher;
use Database\Object\UserAddress as ObjectUserAddress;
use Database\Repository\InformatStaffAssignment;
use Database\Repository\InformatStudent;
use Database\Repository\InformatStudentExtra;
use Database\Repository\InformatStudentSubgroup;
use Database\Repository\InformatStudentSubscription;
use Database\Repository\ModuleSetting;
use Database\Repository\School;
use Database\Repository\SyncStudent;
use Database\Repository\SyncTeacher;
use Database\Repository\UserAddress;
use Helpers\Mapping;
use Informat\Repository\StaffAssignment;
use Informat\Repository\Student;
use Informat\Repository\StudentExtra;
use Informat\Repository\StudentSubgroup;
use Informat\Repository\StudentSubscription;
use Mail\Mail;

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

	public function informat()
	{
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;

		$this->informatStudent();
		$this->informatStudentExtra();
		$this->informatStudentSubscription();
		$this->informatStudentSubgroup();
		$this->syncStudent();

		$this->informatStaff();
		$this->informatStaffAssignment();
		$this->localUserAddress();

		$informatLastSyncTime = $moduleSettingRepo->getByModuleAndKey($module->id, "informatLastSyncTime");
		$informatLastSyncTime->value = Clock::nowAsString("Y-m-d H:i:s");
		$moduleSettingRepo->set($informatLastSyncTime);

		$this->handle();
	}

	public function student()
	{
		$items = (new SyncStudent)->get();
		$items = Arrays::filter($items, fn ($i) => !Strings::equal($i->action, "N"));
		$this->appendToJson("items", array_values($items));

		$this->handle();
	}

	public function syncStudentUpdate($id)
	{
		$lastAdSyncSuccessAction = Helpers::input()->post('lastAdSyncSuccessAction')?->getValue();
		$lastAdSyncTime = Helpers::input()->post('lastAdSyncTime')?->getValue();
		$lastAdSyncError = Helpers::input()->post('lastAdSyncError')?->getValue();
		$action = Helpers::input()->post('action')?->getValue();

		$syncStudentRepo = new SyncStudent;
		$item = $syncStudentRepo->get($id)[0];
		$item->lastAdSyncSuccessAction = (Strings::isNotBlank($lastAdSyncSuccessAction) ? $lastAdSyncSuccessAction : NULL);
		$item->lastAdSyncTime = (Strings::isNotBlank($lastAdSyncTime) ? $lastAdSyncTime : $item->lastAdSyncTime);
		$item->lastAdSyncError = (Strings::isNotBlank($lastAdSyncError) ? $lastAdSyncError : NULL);
		$item->action = (Strings::isNotBlank($action) ? $action : $item->action);
		$syncStudentRepo->set($item);
	}

	public function syncStudentTimeUpdate()
	{
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;

		$studentLastSyncTime = $moduleSettingRepo->getByModuleAndKey($module->id, "studentLastSyncTime");
		$studentLastSyncTime->value = Clock::nowAsString("Y-m-d H:i:s");
		$moduleSettingRepo->set($studentLastSyncTime);

		$this->handle();
	}

	public function syncStudentResetPassword($id, $random = false)
	{
		$id = explode("-", $id);

		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;
		$syncStudentRepo = new SyncStudent;
		$dictionary = $moduleSettingRepo->getByModuleAndKey($module->id, "dictionary")->value;
		$words = explode(PHP_EOL, $dictionary);

		foreach ($id as $_id) {
			$student = $syncStudentRepo->get($_id)[0];
			$password = $student->password;

			if (Strings::equal($random, "true")) {
				$password = Arrays::randElement($words);
				$password .= str_pad(rand(0, pow(10, 2) - 1), 2, '0', STR_PAD_LEFT);
			}

			$student->password = $password;
			$student->action = "UP";
			$syncStudentRepo->set($student);
		}

		$this->setReload();
		$this->handle();
	}

	public function informatStudent()
	{
		$informatStudentRepo = new Student;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStudentRepo = new InformatStudent;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncInformatStudentRepo->db->beginTransaction();

				$informatStudentRepo->setInstituteNumber($institute->instituteNumber);
				$students = $informatStudentRepo->get();

				foreach ($students as $s) {
					$student = $syncInformatStudentRepo->getByInformatUID($s->p_persoon) ?? new ObjectInformatStudent;
					$student->informatUID = $s->p_persoon;
					$student->instituteId = $institute->id;
					$student->name = $s->Naam;
					$student->firstName = $s->Voornaam;
					$student->insz = $s->rijksregnr;

					$syncInformatStudentRepo->set($student);
				}

				$syncInformatStudentRepo->db->commit();
			} catch (\Exception $e) {
				$syncInformatStudentRepo->db->rollback();
			}
		}
	}

	public function informatStudentExtra()
	{
		$informatStudentRepo = new StudentExtra;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStudentExtraRepo = new InformatStudentExtra;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncInformatStudentExtraRepo->db->beginTransaction();

				$informatStudentRepo->setInstituteNumber($institute->instituteNumber);
				$students = $informatStudentRepo->get();

				foreach ($students as $s) {
					$student = $syncInformatStudentExtraRepo->getByInformatUID($s->pointer) ?? new ObjectInformatStudentExtra;
					$student->informatUID = $s->pointer;
					$student->instituteId = $institute->id;
					$student->name = $s->naam;
					$student->firstName = $s->voornaam;
					$student->nickname = $s->Tweedevoornaam;
					$student->masterNumber = $s->Rijksregisternummer;
					$student->bisNumber = $s->BISnummer;

					$syncInformatStudentExtraRepo->set($student);
				}

				$syncInformatStudentExtraRepo->db->commit();
			} catch (\Exception $e) {
				$syncInformatStudentExtraRepo->db->rollback();
			}
		}
	}

	public function informatStudentSubscription()
	{
		$informatStudentSubscriptionRepo = new StudentSubscription;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStudentSubscriptionRepo = new InformatStudentSubscription;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncInformatStudentSubscriptionRepo->db->beginTransaction();

				$informatStudentSubscriptionRepo->setInstituteNumber($institute->instituteNumber);
				$subscriptions = $informatStudentSubscriptionRepo->get();

				foreach ($subscriptions as $s) {
					$subscription = $syncInformatStudentSubscriptionRepo->getByInformatUID($s->PInschrijving) ?? new ObjectInformatStudentSubscription;
					$subscription->informatUID = $s->PInschrijving;
					$subscription->informatStudentUID = $s->PPersoon;
					$subscription->instituteId = $institute->id;
					$subscription->status = $s->Status;
					$subscription->start = Clock::at($s->Begindatum)->format("Y-m-d");
					$subscription->end = (is_null($s->Einddatum) || Strings::isBlank($s->Einddatum) ? NULL : Clock::at($s->Einddatum)->format("Y-m-d"));
					$subscription->grade = $s->Graad;
					$subscription->year = $s->Leerjaar;

					$syncInformatStudentSubscriptionRepo->set($subscription);
				}

				$syncInformatStudentSubscriptionRepo->db->commit();
			} catch (\Exception $e) {
				$syncInformatStudentSubscriptionRepo->db->rollback();
			}
		}
	}

	public function informatStudentSubgroup()
	{
		$informatStudentSubgroupRepo = new StudentSubgroup;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStudentSubsgroupRepo = new InformatStudentSubgroup;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncInformatStudentSubsgroupRepo->db->beginTransaction();

				$informatStudentSubgroupRepo->setInstituteNumber($institute->instituteNumber);
				$subscriptions = $informatStudentSubgroupRepo->get();

				foreach ($subscriptions as $s) {
					$subgroup = $syncInformatStudentSubsgroupRepo->getByInformatStudentUID($s->p_persoon) ?? new ObjectInformatStudentSubgroup;
					$subgroup->informatStudentUID = $s->p_persoon;
					$subgroup->class = $s->Klascode;

					$syncInformatStudentSubsgroupRepo->set($subgroup);
				}

				$syncInformatStudentSubsgroupRepo->db->commit();
			} catch (\Exception $e) {
				$syncInformatStudentSubsgroupRepo->db->rollback();
			}
		}
	}

	public function syncStudent()
	{
		$syncStudentRepo = new SyncStudent;
		$informatStudentRepo = new InformatStudent;
		$informatStudentSubscriptionRepo = new InformatStudentSubscription;
		$informatStudentSubgroupRepo = new InformatStudentSubgroup;
		$schoolInstituteRepo = new SchoolInstitute;
		$schoolRepo = new School;
		$syncToAdFrom = (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule('synchronisation')->id, "syncToAdFrom")->value;

		try {
			$syncStudentRepo->db->beginTransaction();

			foreach ($informatStudentRepo->get() as $istudent) {
				$subscriptions = $informatStudentSubscriptionRepo->getByInformatStudentUIDAndStatus($istudent->informatUID, 0);
				if (empty($subscriptions)) continue;

				$institute = $schoolInstituteRepo->get($istudent->instituteId)[0];
				$school = $schoolRepo->get($institute->schoolId)[0];
				$subgroup = $informatStudentSubgroupRepo->getByInformatStudentUID($istudent->informatUID);

				$lastSubscription = Arrays::last($subscriptions);

				if ((int)$lastSubscription->grade < (int)$syncToAdFrom) continue;

				$student = $syncStudentRepo->getByInformatUID($istudent->informatUID) ?? new ObjectSyncStudent;

				$uid = $istudent->informatUID;

				$firstNameClean = Input::clean($istudent->firstName);
				$nameClean = Input::clean($istudent->name);

				$memberOf = SYNC_DEFAULT_MEMBEROF_STUDENT;
				$memberOf = str_replace("{{school:adSecGroupPostfix}}", $school->adSecGroupPostfix, $memberOf);

				$ou = SYNC_DEFAULT_OU_STUDENT;
				$ou = str_replace("{{school:adUserDescription}}", $school->adUserDescription, $ou);

				$action = "N";
				if (Strings::isBlank($student->lastAdSyncTime)) $action = "C" . PHP_EOL . "UP" . PHP_EOL . "U" . PHP_EOL . "A";
				else if (!is_null($lastSubscription?->end) && Clock::now()->isAfter(Clock::at($lastSubscription->end))) $action = "DA";
				else if (Strings::isNotBlank($student->ou) && !Strings::equal($student->ou, $ou)) $action = "U" . PHP_EOL . "M";

				$lastSuccessActions = explode(PHP_EOL, $student->lastAdSyncSuccessAction);
				foreach ($lastSuccessActions as $lsa) {
					if (Strings::contains($action, $lsa)) {
						$action = str_replace([$lsa, "{$lsa}" . PHP_EOL], "", $action);
					}
				}

				if (strlen(trim($action)) == 0) $action = "N";

				$student->informatUID = $istudent->informatUID;
				$student->instituteId = $istudent->instituteId;
				$student->uid = $uid;
				$student->name = $istudent->name;
				$student->firstName = $istudent->firstName;
				$student->displayName = trim("{$istudent->firstName} {$istudent->name}");
				$student->email = strtolower("{$firstNameClean}.{$nameClean}@student." . EMAIL_SUFFIX);
				$student->description = $school->adUserDescription;
				$student->companyName = SYNC_DEFAULT_COMPANY_NAME;
				$student->memberOf = $memberOf;
				$student->samAccountName = substr(explode("@", $student->email)[0], 0, 19);
				$student->ou = $ou;
				$student->action = $action;
				$student->class = $subgroup->class;

				$student->link();
				$syncStudentRepo->set($student);
			}

			$syncStudentRepo->db->commit();
		} catch (\Exception $e) {
			$syncStudentRepo->db->rollback();
			die(var_dump($e->getMessage()));
		}
	}

	public function informatStaff()
	{
		$informatStaffRepo = new Staff;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStaffRepo = new InformatStaff;

		foreach ($instituteRepo->get() as $institute) {
			try {
				$syncInformatStaffRepo->db->beginTransaction();

				$informatStaffRepo->setInstituteNumber($institute->instituteNumber);
				$staff = $informatStaffRepo->get();

				foreach ($staff as $s) {
					$syncStaff = $syncInformatStaffRepo->getByInformatUID($s->p_persoon) ?? new ObjectInformatStaff;
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

					$syncInformatStaffRepo->set($syncStaff);
				}

				$syncInformatStaffRepo->db->commit();
			} catch (\Exception $e) {
				$syncInformatStaffRepo->db->rollback();
			}
		}
	}

	public function informatStaffAssignment()
	{
		$informatStaffAssignmentRepo = new StaffAssignment;
		$instituteRepo = new SchoolInstitute;
		$syncInformatStaffAssignmentRepo = new InformatStaffAssignment;

		try {
			$syncInformatStaffAssignmentRepo->db->beginTransaction();
			foreach ($instituteRepo->get() as $institute) {

				$informatStaffAssignmentRepo->setInstituteNumber($institute->instituteNumber);
				$staffAssignments = $informatStaffAssignmentRepo->get();

				foreach ($staffAssignments as $s) {
					$syncStaffAssignment = $syncInformatStaffAssignmentRepo->getByInformatUID($s->POpdr) ?? new ObjectInformatStaffAssignment;
					$syncStaffAssignment->informatUID = $s->POpdr;
					$syncStaffAssignment->informatStaffUID = $s->p_persoon;
					$syncStaffAssignment->masterNumber = $s->Stamnummer;
					$syncStaffAssignment->instituteNumber = $s->Instelnr;
					$syncStaffAssignment->start = Clock::at($s->Begindatum)->format("Y-m-d");
					$syncStaffAssignment->end = Clock::at($s->Einddatum)->format("Y-m-d");
					$syncInformatStaffAssignmentRepo->set($syncStaffAssignment);
				}
			}

			$syncInformatStaffAssignmentRepo->db->commit();
		} catch (\Exception $e) {
			$syncInformatStaffAssignmentRepo->db->rollback();
		}
	}

	public function localUserAddress()
	{
		$localUserRepo = new LocalUser;
		$localUserAddressRepo = new UserAddress;
		$informatStaffRepo = new InformatStaff;

		try {
			$informatStaffRepo->db->beginTransaction();

			foreach ($localUserRepo->get() as $localUser) {
				$informatStaff = $informatStaffRepo->getBySchoolEmail($localUser->username);
				if (is_null($informatStaff)) continue;

				$userAddresses = $localUserAddressRepo->getByUserId($localUser->id);
				$currentAddress = $localUserAddressRepo->getCurrentByUserId($localUser->id);
				$createAddress = false;

				if (is_null($currentAddress)) $createAddress = true;
				else if (!Strings::equal($currentAddress->addressHash, $informatStaff->addressHash)) {
					foreach ($userAddresses as $userAddress) {
						if (!Strings::equal($userAddress->addressHash, $informatStaff->addressHash)) {
							$createAddress = true;
						} else {
							$createAddress = false;
							$userAddress->current = true;
							$localUserAddressRepo->set($userAddress);
							break;
						}
					}
				}

				if ($createAddress) {
					foreach ($userAddresses as $userAddress) {
						$userAddress->current = false;
						$localUserAddressRepo->set($userAddress);
					}

					$userAddress = new ObjectUserAddress([
						"userId" => $localUser->id,
						"street" => $informatStaff->addressStreet,
						"number" => $informatStaff->addressNumber,
						"bus" => $informatStaff->addressBus,
						"zipcode" => $informatStaff->addressZipcode,
						"city" => $informatStaff->addressCity,
						"country" => $informatStaff->addressCountryFull,
						"current" => true
					]);
					$localUserAddressRepo->set($userAddress);
				}
			}

			$informatStaffRepo->db->commit();
		} catch (\Exception $e) {
			$informatStaffRepo->db->rollback();
		}
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
