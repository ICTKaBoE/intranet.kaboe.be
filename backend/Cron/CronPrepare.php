<?php

namespace Cron;

use Security\Input;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\SyncStudent;
use Database\Repository\ModuleSetting;
use Database\Repository\InformatStudent;
use Database\Repository\SchoolInstitute;
use Database\Repository\InformatStudentSubgroup;
use Database\Repository\InformatStudentSubscription;
use Database\Object\SyncStudent as ObjectSyncStudent;
use Security\User;

abstract class CronPrepare
{
	public static function Sync()
	{
		self::Student();
	}

	private static function Student()
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
			$informatStudents = $informatStudentRepo->get();

			foreach ($informatStudents as $informatStudent) {
				$informatStudentSubscriptions = $informatStudentSubscriptionRepo->getByInformatStudentUIDAndStatus($informatStudent->informatUID, 0);
				if (empty($informatStudentSubscriptions)) continue;

				$institute = Arrays::first($schoolInstituteRepo->getByInstituteNumber($informatStudent->instituteId));
				$school = Arrays::first($schoolRepo->get($institute->schoolId));
				$subgroup = $informatStudentSubgroupRepo->getByInformatStudentUID($informatStudent->informatUID);

				$lastSubscription = Arrays::last($informatStudentSubscriptions);
				if ((int)$lastSubscription->grade < (int)$syncToAdFrom) continue;

				$ou = str_replace("{{school:adUserDescription}}", $school->adUserDescription, SYNC_DEFAULT_OU_STUDENT);

				$syncStudent = $syncStudentRepo->getByInformatUID($informatStudent->informatUID) ?? new ObjectSyncStudent;
				$syncStudent->informatUID = $informatStudent->informatUID;
				$syncStudent->instituteId = $informatStudent->instituteId;
				$syncStudent->uid = $informatStudent->informatUID;
				$syncStudent->name = $informatStudent->name;
				$syncStudent->firstName = $informatStudent->firstName;
				$syncStudent->displayName = "{$syncStudent->firstName} {$syncStudent->name}";
				$syncStudent->email = strtolower(Input::clean($syncStudent->firstName) . "." . Input::clean($syncStudent->name) . "@student." . EMAIL_SUFFIX);
				$syncStudent->description = $school->adUserDescription;
				$syncStudent->companyName = SYNC_DEFAULT_COMPANY_NAME;
				$syncStudent->password = is_null($syncStudent->id) ? User::generatePassword() : $syncStudent->password;
				$syncStudent->memberOf = str_replace("{{school:adSecGroupPostfix}}", $school->adSecGroupPostfix, SYNC_DEFAULT_MEMBEROF_STUDENT);
				$syncStudent->samAccountName = substr(explode("@", $syncStudent->email)[0], 0, 19);
				$syncStudent->ou = $ou;
				$syncStudent->active = true;
				$syncStudent->class = $subgroup->class;

				$syncStudent->action = "N";
				if (!is_null($lastSubscription?->end) && Clock::now()->isAfter(Clock::at($lastSubscription->end))) {
					$syncStudent->action = "DA";
					$syncStudent->active = false;
				} else if (Strings::isNotBlank($syncStudent->ou) && !Strings::equal($syncStudent->ou, $ou)) $syncStudent->action = "U<br />M";
				else if (Strings::isBlank($syncStudent->lastAdSyncTime)) {
					$syncStudent->action = implode(PHP_EOL, ["C", "UP", "U", "A"]);
					if (Strings::isBlank($syncStudent->password)) $syncStudent->password = User::generatePassword();
				}

				$lastSuccessActions = explode(PHP_EOL, $syncStudent->lastAdSyncSuccessAction);
				foreach ($lastSuccessActions as $lsa) {
					if (Strings::contains($syncStudent->action, $lsa)) {
						$syncStudent->action = str_replace([$lsa, $lsa . PHP_EOL], "", $syncStudent->action);
					}
				}

				if (strlen(trim($syncStudent->action)) == 0) $syncStudent->action = "N";
				$syncStudentRepo->set($syncStudent);
			}

			$syncStudentRepo->db->commit();
		} catch (\Exception $e) {
			$syncStudentRepo->db->rollback();
		}
	}
}
