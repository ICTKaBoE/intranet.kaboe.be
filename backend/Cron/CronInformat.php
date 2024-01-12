<?php

namespace Cron;

use Ouzo\Utilities\Clock;
use Database\Repository\Log;
use Informat\Repository\Staff;
use Database\Repository\Module;
use Informat\Repository\Student;
use Mapper\InformatStaffDBMapper;
use Mapper\InformatStudentDBMapper;
use Database\Repository\SchoolClass;
use Informat\Repository\StudentExtra;
use Database\Repository\InformatStaff;
use Database\Repository\ModuleSetting;
use Informat\Repository\StaffFreeField;
use Database\Repository\InformatStudent;
use Database\Repository\SchoolInstitute;
use Informat\Repository\StaffAssignment;
use Informat\Repository\StudentSubgroup;
use Mapper\InformatStudentExtraDBMapper;
use Mapper\InformatStaffFreeFieldDBMapper;
use Mapper\InformatStaffAssignmentDBMapper;
use Mapper\InformatStudentSubgroupDBMapper;
use Informat\Repository\StudentSubscription;
use Database\Repository\InformatStudentExtra;
use Database\Repository\InformatStaffFreeField;
use Mapper\InformatStudentSubscriptionDBMapper;
use Database\Repository\InformatStaffAssignment;
use Database\Repository\InformatStudentSubgroup;
use Database\Repository\InformatStudentSubscription;
use Database\Object\SchoolClass as ObjectSchoolClass;
use Database\Object\InformatStaff as ObjectInformatStaff;
use Database\Object\InformatStudent as ObjectInformatStudent;
use Database\Object\InformatStudentExtra as ObjectInformatStudentExtra;
use Database\Object\InformatStaffFreeField as ObjectInformatStaffFreeField;
use Database\Object\InformatStaffAssignment as ObjectInformatStaffAssignment;
use Database\Object\InformatStudentSubgroup as ObjectInformatStudentSubgroup;
use Database\Object\InformatStudentSubscription as ObjectInformatStudentSubscription;

abstract class CronInformat
{
	public static function Sync()
	{
		// STAFF
		self::Staff();
		self::StaffAssignment();
		self::StaffFreeFields();

		// STUDENTS
		self::Student();
		self::StudentExtra();
		self::StudentSubgroup();
		self::StudentSubscription();

		// UPDATE DATABASE
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;
		$informatLastSyncTime = $moduleSettingRepo->getByModuleAndKey($module->id, "informatLastSyncTime");
		$informatLastSyncTime->value = Clock::nowAsString("Y-m-d H:i:s");
		$moduleSettingRepo->set($informatLastSyncTime);
	}

	private static function Staff()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStaffRepo = new Staff;
		$informatStaffRepo = new InformatStaff;

		foreach ($institutes as $institute) {
			$informatStaffRepo->db->beginTransaction();

			$iStaffRepo->setInstituteNumber($institute->instituteNumber);
			$staff = $iStaffRepo->get();

			foreach ($staff as $s) {
				$staffObject = $informatStaffRepo->getByInformatUID($s->p_persoon) ?? (new ObjectInformatStaff);
				$staffObject = (new InformatStaffDBMapper)->map($staffObject, $s);
				$newId = $informatStaffRepo->set($staffObject);

				Log::write(userId: 0, description: "Added/Updated Informat Staff with ID " . $staffObject->id ?? $newId . " and with Informat UID {$s->p_persoon}");
			}

			$informatStaffRepo->db->commit();
		}
	}

	private static function StaffAssignment()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStaffAssignmentRepo = new StaffAssignment;
		$informatStaffAssignmentRepo = new InformatStaffAssignment;

		foreach ($institutes as $institute) {
			$informatStaffAssignmentRepo->db->beginTransaction();

			$iStaffAssignmentRepo->setInstituteNumber($institute->instituteNumber);
			$staffAssignments = $iStaffAssignmentRepo->get();

			foreach ($staffAssignments as $sa) {
				$staffAssignmentObject = $informatStaffAssignmentRepo->getByInformatUID($sa->POpdr) ?? (new ObjectInformatStaffAssignment);
				$staffAssignmentObject = (new InformatStaffAssignmentDBMapper)->map($staffAssignmentObject, $sa);
				$informatStaffAssignmentRepo->set($staffAssignmentObject);
			}

			$informatStaffAssignmentRepo->db->commit();
		}
	}

	private static function StaffFreeFields()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStaffFreeFieldRepo = new StaffFreeField;
		$informatStaffRepo = new InformatStaff;
		$informatStaffFreeFieldRepo = new InformatStaffFreeField;

		foreach ($institutes as $institute) {
			$informatStaffFreeFieldRepo->db->beginTransaction();

			$iStaffFreeFieldRepo->setInstituteNumber($institute->instituteNumber);
			$staffFreeFields = $iStaffFreeFieldRepo->get();

			foreach ($staffFreeFields as $sff) {
				$staffId = $informatStaffRepo->getByInformatUID($sff->pPersoon)->id;
				$staffFreeFieldObject = $informatStaffFreeFieldRepo->getByStaffIdAndDescription($staffId, $sff->OmschrijvingVrijVeld) ?? (new ObjectInformatStaffFreeField);
				$staffFreeFieldObject = (new InformatStaffFreeFieldDBMapper)->map($staffFreeFieldObject, $sff);
				$staffFreeFieldObject->informatStaffId = $staffId;
				$informatStaffFreeFieldRepo->set($staffFreeFieldObject);
			}

			$informatStaffFreeFieldRepo->db->commit();
		}
	}

	private static function Student()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStudentRepo = new Student;
		$informatStudentRepo = new InformatStudent;

		foreach ($institutes as $institute) {
			$informatStudentRepo->db->beginTransaction();

			$iStudentRepo->setInstituteNumber($institute->instituteNumber);
			$students = $iStudentRepo->get();

			foreach ($students as $s) {
				$studentObject = $informatStudentRepo->getByInformatUID($s->p_persoon) ?? (new ObjectInformatStudent);
				$studentObject = (new InformatStudentDBMapper)->map($studentObject, $s);
				$informatStudentRepo->set($studentObject);
			}

			$informatStudentRepo->db->commit();
		}
	}

	private static function StudentExtra()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStudentExtraRepo = new StudentExtra;
		$informatStudentExtraRepo = new InformatStudentExtra;

		foreach ($institutes as $institute) {
			$informatStudentExtraRepo->db->beginTransaction();

			$iStudentExtraRepo->setInstituteNumber($institute->instituteNumber);
			$studentExtras = $iStudentExtraRepo->get();

			foreach ($studentExtras as $se) {
				$studentExtraObject = $informatStudentExtraRepo->getByInformatUID($se->p_persoon) ?? (new ObjectInformatStudentExtra);
				$studentExtraObject = (new InformatStudentExtraDBMapper)->map($studentExtraObject, $se);
				$studentExtraObject->instituteId = $institute->instituteNumber;
				$informatStudentExtraRepo->set($studentExtraObject);
			}

			$informatStudentExtraRepo->db->commit();
		}
	}

	private static function StudentSubgroup()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStudentSubgroupRepo = new StudentSubgroup;
		$informatStudentSubgroupRepo = new InformatStudentSubgroup;
		$schoolClassRepo = new SchoolClass;

		foreach ($institutes as $institute) {
			$informatStudentSubgroupRepo->db->beginTransaction();

			$iStudentSubgroupRepo->setInstituteNumber($institute->instituteNumber);
			$studentSubgroups = $iStudentSubgroupRepo->get();

			foreach ($studentSubgroups as $ss) {
				$studentSubgroupObject = $informatStudentSubgroupRepo->getByInformatStudentUID($ss->p_persoon) ?? (new ObjectInformatStudentSubgroup);
				$studentSubgroupObject = (new InformatStudentSubgroupDBMapper)->map($studentSubgroupObject, $ss);
				$studentSubgroupObject->instituteId = $institute->instituteNumber;
				$informatStudentSubgroupRepo->set($studentSubgroupObject);

				$class = $schoolClassRepo->getBySchoolIdAndClassName($institute->schoolId, $studentSubgroupObject->class) ?? (new ObjectSchoolClass);
				$class->schoolId = $institute->schoolId;
				$class->name = $studentSubgroupObject->class;
				$class->teacher = $ss->Klastitularis;
				$class->grade = $ss->Graad;
				$class->year = $ss->Leerjaar;
				$schoolClassRepo->set($class);
			}

			$informatStudentSubgroupRepo->db->commit();
		}
	}

	private static function StudentSubscription()
	{
		$institutes = (new SchoolInstitute)->get();
		$iStudentSubscriptionRepo = new StudentSubscription;
		$informatStudentSubscriptionRepo = new InformatStudentSubscription;

		foreach ($institutes as $institute) {
			$informatStudentSubscriptionRepo->db->beginTransaction();

			$iStudentSubscriptionRepo->setInstituteNumber($institute->instituteNumber);
			$studentSubscriptions = $iStudentSubscriptionRepo->get();

			foreach ($studentSubscriptions as $ss) {
				$studentSubscriptionObject = $informatStudentSubscriptionRepo->getByInformatUID($ss->PInschrijving) ?? (new ObjectInformatStudentSubscription);
				$studentSubscriptionObject = (new InformatStudentSubscriptionDBMapper)->map($studentSubscriptionObject, $ss);
				$informatStudentSubscriptionRepo->set($studentSubscriptionObject);
			}

			$informatStudentSubscriptionRepo->db->commit();
		}
	}
}
