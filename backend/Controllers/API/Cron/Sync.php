<?php

namespace Controllers\API\Cron;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Database\Repository\Navigation;
use Database\Object\Sync as ObjectSync;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Employee;
use M365\Repository\User as RepositoryUser;
use Database\Repository\Sync as RepositorySync;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\School;
use Database\Repository\SchoolInstitute;
use Ouzo\Utilities\Clock;
use Security\Input;
use Security\User;

use function Ramsey\Uuid\v1;

abstract class Sync
{
    static public function Prepare()
    {
        // $prepareEmployee = self::PrepareEmployee();
        $prepareStudent = self::PrepareStudent();

        return ($prepareEmployee && $prepareStudent);
    }

    private static function PrepareEmployee()
    {
        $navRepo = new Navigation;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $m365UserRepo = new RepositoryUser;
        $informatEmployeeRepo = new Employee;

        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_status = $_settings['informat']['ownfield']['status'];
        $_mainSchool = $_settings['informat']['ownfield']['mainSchool'];
        $_firstName = $_settings['informat']['ownfield']['createEmailWith'];

        $currentEmployees = $m365UserRepo->getGroupMembersByGroupId($_settings['m365']['group']['employee'], ['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes']);
        $currentEmployees = Arrays::filter($currentEmployees, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        $informatEmployees = $informatEmployeeRepo->get();

        // Employees not in Entra ID and are active in mgmt - CREATE
        $informatEmployeeActiveInMgmtIds = Arrays::map(Arrays::filter($informatEmployees, fn($i) => $i->active), fn($ie) => $ie->informatId);
        $m365CurrentEmployeeIds = Arrays::map($currentEmployees, fn($ce) => $ce->getEmployeeId());
        $notInEntraEmployeeIds = Arrays::filter($informatEmployeeActiveInMgmtIds, fn($iei) => !Arrays::filter($m365CurrentEmployeeIds, fn($cei) => Strings::equal($cei, $iei) || Strings::equal($cei, "P{$iei}")));
        $notInEntraEmployees = Arrays::map($notInEntraEmployeeIds, fn($nei) => Arrays::first(Arrays::filter($informatEmployees, fn($ie) => Strings::equal($ie->informatId, $nei))));
        $notInEntraEmployees = Arrays::filter($notInEntraEmployees, fn($nieei) => Strings::equal(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($nieei->id, 2, $_status))->value, "IN DIENST"));

        foreach ($notInEntraEmployees as $notInEntraEmployee) {
            $mainSchool = $schoolRepo->getByName($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($notInEntraEmployee->id, 2, $_mainSchool)->value);

            $GivenName = (Strings::equalsIgnoreCase(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($notInEntraEmployee->id, 2, $_firstName)->value ?: "Officiële voornaam"), "officiële voornaam") ? $notInEntraEmployee->firstName : $notInEntraEmployee->extraFirstName);
            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $GivenName, $notInEntraEmployee->name);
            $EmailAddress = Input::createEmail($_settings['format']['email'], $GivenName, $notInEntraEmployee->name, EMAIL_SUFFIX);
            $SamAccountName = substr(Arrays::first(explode("@", $EmailAddress)), 0, 20);
            $MemberOf = [];
            $OtherAttributes = [];

            $functions = $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdAndSection($notInEntraEmployee->id, 2);
            $functions = Arrays::filter($functions, fn($ff) => Strings::contains($ff->name, " - Functie "));

            $schools = Arrays::map($functions, fn($f) => $f->name);
            $schools = Arrays::map($schools, fn($d) => Arrays::first(explode(" - ", $d)));
            $schools = array_unique(array_values($schools));

            foreach ($_settings['default']['memberOf']['employee'] as $mof) {
                if (is_array($mof)) {
                    foreach ($schools as $school) {
                        $school = $schoolRepo->getByName($school);
                        if (is_null($school->adSecGroupPart)) continue;
                        $MemberOf[] = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof[0]);
                    }
                } else {
                    if (is_null($mainSchool->adSecGroupPart)) continue;
                    $MemberOf[] = str_replace("{{school:adSecGroupPart}}", $mainSchool->adSecGroupPart, $mof);
                }
            }
            $MemberOf = array_unique($MemberOf);

            $departments = Arrays::map($schools, fn($d) => $schoolRepo->getByName($d)->name);
            $departments = implode(", ", $departments);

            $jobtitles = Arrays::map($functions, fn($f) => Arrays::first(explode(' (', $f->value)));
            $jobtitles = array_unique(array_values($jobtitles));
            $jobtitles = implode(", ", $jobtitles);

            $sync = $syncRepo->getByEmployeeId($notInEntraEmployee->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->employeeId = $notInEntraEmployee->informatId;
            $sync->givenName = $GivenName;
            $sync->surname = $notInEntraEmployee->name;
            $sync->displayName = $DisplayName;
            $sync->emailAddress = $EmailAddress;
            $sync->samAccountName = $SamAccountName;
            $sync->userPrincipalName = $EmailAddress;
            $sync->companyName = $_settings['default']['companyName']["employee"];
            $sync->department = $departments;
            $sync->jobTitle = $jobtitles;
            $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
            $sync->otherAttributes = empty($OtherAttributes) ? null : $OtherAttributes;
            $sync->password = User::generatePassword();
            $sync->ou = $_settings['default']['ou']['employee'];
            $sync->action = "C";

            $syncRepo->set($sync);
        }

        // Employees disabled in Entra ID and but active in management - ENABLE
        $informatEmployeeActiveInMgmtIds = Arrays::map(Arrays::filter($informatEmployees, fn($i) => $i->active), fn($ie) => $ie->informatId);
        $m365CurrentDisabledEmployeesIds = Arrays::map(Arrays::filter($currentEmployees, fn($ce) => !$ce->getAccountEnabled()), fn($ce) => $ce->getEmployeeId());
        $activeButInactiveIds = Arrays::filter($informatEmployeeActiveInMgmtIds, fn($ieaimi) => Arrays::filter($m365CurrentDisabledEmployeesIds, fn($mcdei) => Strings::equal($mcdei, $ieaimi) || Strings::equal($mcdei, "P{$ieaimi}")));
        $activeButInactiveEmployees = Arrays::map($activeButInactiveIds, fn($aii) => Arrays::first(Arrays::filter($informatEmployees, fn($ie) => Strings::equal($ie->informatId, $aii))));
        $activeButInactiveEmployees = Arrays::filter($activeButInactiveEmployees, fn($nieei) => Strings::equal(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($nieei->id, 2, $_status))->value, "IN DIENST"));

        foreach ($activeButInactiveEmployees as $activeButInactiveEmployee) {
            $mainSchool = $schoolRepo->getByName($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($activeButInactiveEmployee->id, 2, $_mainSchool)->value);

            $GivenName = (Strings::equalsIgnoreCase(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($activeButInactiveEmployee->id, 2, $_firstName)->value ?: "Officiële voornaam"), "officiële voornaam") ? $activeButInactiveEmployee->firstName : $activeButInactiveEmployee->extraFirstName);
            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $GivenName, $activeButInactiveEmployee->name);
            $EmailAddress = Input::createEmail($_settings['format']['email'], $GivenName, $activeButInactiveEmployee->name, EMAIL_SUFFIX);
            $SamAccountName = substr(Arrays::first(explode("@", $EmailAddress)), 0, 20);
            $MemberOf = [];
            $OtherAttributes = [];

            $functions = $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdAndSection($activeButInactiveEmployee->id, 2);
            $functions = Arrays::filter($functions, fn($ff) => Strings::contains($ff->name, " - Functie "));

            $schools = Arrays::map($functions, fn($f) => $f->name);
            $schools = Arrays::map($schools, fn($d) => Arrays::first(explode(" - ", $d)));
            $schools = array_unique(array_values($schools));

            foreach ($_settings['default']['memberOf']['employee'] as $mof) {
                if (is_array($mof)) {
                    foreach ($schools as $school) {
                        $school = $schoolRepo->getByName($school);
                        if (is_null($school->adSecGroupPart)) continue;
                        $MemberOf[] = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof[0]);
                    }
                } else {
                    if (is_null($mainSchool->adSecGroupPart)) continue;
                    $MemberOf[] = str_replace("{{school:adSecGroupPart}}", $mainSchool->adSecGroupPart, $mof);
                }
            }
            $MemberOf = array_unique($MemberOf);

            $departments = Arrays::map($schools, fn($d) => $schoolRepo->getByName($d)->name);
            $departments = implode(", ", $departments);

            $jobtitles = Arrays::map($functions, fn($f) => Arrays::first(explode(' (', $f->value)));
            $jobtitles = array_unique(array_values($jobtitles));
            $jobtitles = implode(", ", $jobtitles);

            $sync = $syncRepo->getByEmployeeId($activeButInactiveEmployee->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->employeeId = $activeButInactiveEmployee->informatId;
            $sync->givenName = $GivenName;
            $sync->surname = $activeButInactiveEmployee->name;
            $sync->displayName = $DisplayName;
            $sync->companyName = $_settings['default']['companyName']["employee"];
            $sync->department = $departments;
            $sync->jobTitle = $jobtitles;
            $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
            $sync->otherAttributes = empty($OtherAttributes) ? null : $OtherAttributes;
            $sync->password = User::generatePassword();
            $sync->ou = $_settings['default']['ou']['employee'];
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->action = "E";

            $syncRepo->set($sync);
        }

        // Employees active in Entra ID and inactive in mgmt - DISABLE
        $informatEmployeeInMgmtIds = Arrays::map($informatEmployees, fn($ie) => $ie->informatId);
        $m365CurrentEnabledEmployeesIds = Arrays::map(Arrays::filter($currentEmployees, fn($ce) => $ce->getAccountEnabled()), fn($ce) => $ce->getEmployeeId());
        $inactiveButActiveIds = Arrays::filter($informatEmployeeInMgmtIds, fn($ieimi) => Arrays::filter($m365CurrentEnabledEmployeesIds, fn($mceei) => Strings::equal($mceei, $ieimi) || Strings::equal($mceei, "P{$ieimi}")));
        $inactiveButActiveEmployees = Arrays::map($inactiveButActiveIds, fn($iai) => Arrays::first(Arrays::filter($informatEmployees, fn($ie) => Strings::equal($ie->informatId, $iai))));
        $inactiveButActiveEmployees = Arrays::filter($inactiveButActiveEmployees, fn($nieei) => !$nieei->active);
        $inactiveButActiveEmployees = Arrays::filter($inactiveButActiveEmployees, fn($nieei) => !Strings::equal(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($nieei->id, 2, $_status))->value, "IN DIENST"));

        foreach ($inactiveButActiveEmployees as $inactiveButActiveEmployee) {
            $sync = $syncRepo->getByEmployeeId($inactiveButActiveEmployee->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->employeeId = $inactiveButActiveEmployee->informatId;
            $sync->givenName = null;
            $sync->surname = null;
            $sync->displayName = null;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->companyName = null;
            $sync->department = null;
            $sync->jobTitle = null;
            $sync->memberOf = null;
            $sync->otherAttributes = null;
            $sync->password = null;
            $sync->ou = null;
            $sync->action = "D";

            $syncRepo->set($sync);
        }

        // Employees in Entra ID and active - UPDATE
        foreach ($informatEmployees as $informatEmployee) {
            if (Arrays::contains(Arrays::map($notInEntraEmployees, fn($e) => $e->informatId), $informatEmployee->informatId)) continue;
            if (Arrays::contains(Arrays::map($activeButInactiveEmployees, fn($e) => $e->informatId), $informatEmployee->informatId)) continue;
            if (Arrays::contains(Arrays::map($inactiveButActiveEmployees, fn($e) => $e->informatId), $informatEmployee->informatId)) continue;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentEmployees, fn($ce) => Strings::equal($ce->getEmployeeId(), $informatEmployee->informatId) || Strings::equal($ce->getEmployeeId(), "P{$informatEmployee->informatId}")));
            if (!$m365User) continue;
            $memberOfM365 = $m365UserRepo->getMemberOf($m365User->getId(), ['onPremisesDomainName', 'displayName']);
            $memberOfM365 = Arrays::filter($memberOfM365, fn($m) => $m->getOnPremisesDomainName() !== null);
            $memberOfM365 = Arrays::map($memberOfM365, fn($m) => $m->getDisplayName());

            $mainSchool = $schoolRepo->getByName($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_mainSchool)->value);

            $GivenName = (Strings::equalsIgnoreCase(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_firstName)->value ?: "Officiële voornaam"), "officiële voornaam") ? $informatEmployee->firstName : $informatEmployee->extraFirstName);
            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $GivenName, $informatEmployee->name);
            $MemberOf = [];
            $OtherAttributes = [];

            $functions = $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdAndSection($informatEmployee->id, 2);
            $functions = Arrays::filter($functions, fn($ff) => Strings::contains($ff->name, " - Functie "));

            $schools = Arrays::map($functions, fn($f) => $f->name);
            $schools = Arrays::map($schools, fn($d) => Arrays::first(explode(" - ", $d)));
            $schools = array_unique(array_values($schools));

            foreach ($_settings['default']['memberOf']['employee'] as $mof) {
                if (is_array($mof)) {
                    foreach ($schools as $school) {
                        $school = $schoolRepo->getByName($school);
                        if (is_null($school->adSecGroupPart)) continue;
                        $_mof = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof[0]);

                        if (!Arrays::contains($memberOfM365, $_mof)) $MemberOf[] = $_mof;
                    }
                } else {
                    if (is_null($mainSchool->adSecGroupPart)) continue;
                    $mof = str_replace("{{school:adSecGroupPart}}", $mainSchool->adSecGroupPart, $mof);
                    if (!Arrays::contains($memberOfM365, $mof)) $MemberOf[] = $mof;
                }
            }
            $MemberOf = array_unique($MemberOf);

            $departments = Arrays::map($schools, fn($d) => $schoolRepo->getByName($d)->name);
            $departments = trim(implode(", ", $departments));

            $jobtitles = Arrays::map($functions, fn($f) => Arrays::first(explode(' (', $f->value)));
            $jobtitles = array_unique(array_values($jobtitles));
            $jobtitles = trim(implode(", ", $jobtitles));

            $sync = $syncRepo->getByEmployeeId($informatEmployee->informatId) ?? new ObjectSync;
            if ($sync->lastAction == "D") continue;

            $_functions = [];
            foreach ($functions as $function) {
                $school = $schoolRepo->getByName(Arrays::first(explode(" - ", $function->name)));
                $functionCode = str_replace(")", "", Arrays::last(explode("(", $function->value)));

                if (!Arrays::keyExists($_functions, $school->adJobTitlePrefix)) $_functions[$school->adJobTitlePrefix] = [];
                $_functions[$school->adJobTitlePrefix][] = $functionCode;
            }

            if (!empty($_functions)) {
                $ea1 = [];

                foreach ($_functions as $_school => $_codes) {
                    foreach ($_codes as $_code) {
                        $ea1[] = "{$_school}:{$_code}";
                    }
                }

                $OtherAttributes["extensionAttribute1"] = implode(" ", $ea1);
                if (Strings::equal($OtherAttributes["extensionAttribute1"], $m365User->getOnPremisesExtensionAttributes()->getExtensionAttribute1())) unset($OtherAttributes["extensionAttribute1"]);
            }

            $sync->type = "E";
            $sync->givenName = Strings::equal($GivenName, $m365User->getGivenName()) ? null : $GivenName;
            $sync->surname = Strings::equal($informatEmployee->name, $m365User->getSurname()) ? null : $informatEmployee->name;
            $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
            $sync->companyName = Strings::equal($_settings['default']['companyName']["employee"], $m365User->getCompanyName()) ? null : $_settings['default']['companyName']["employee"];
            $sync->department = Strings::equal($departments, $m365User->getDepartment()) ? null : ($departments ?: null);
            $sync->jobTitle = Strings::equal($jobtitles, $m365User->getJobTitle()) ? null : ($jobtitles ?: null);
            $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
            $sync->otherAttributes = empty($OtherAttributes) ? null : $OtherAttributes;
            $sync->employeeId = $informatEmployee->informatId;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->password = null;
            $sync->ou = null;
            $sync->action = "U";

            if ($sync->noUpdate()) $sync->action = null;
            $syncRepo->set($sync);
        }

        return true;
    }

    private static function PrepareStudent()
    {
        $navRepo = new Navigation;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $instituteRepo = new SchoolInstitute;
        $m365UserRepo = new RepositoryUser;
        $informatStudentRepo = new Student;
        $informatRegistrationRepo = new Registration;
        $informatRegistrationClassRepo = new RegistrationClass;
        $informatClassgroupRepo = new ClassGroup;
        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;

        $currentStudents = $m365UserRepo->getGroupMembersByGroupId($_settings['m365']['group']['student'], ['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes', 'onPremisesDistinguishedName']);
        $currentStudents = Arrays::filter($currentStudents, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        $informatStudents = $informatStudentRepo->get();

        // Students not in Entra ID and are active in mgmt - CREATE
        $informatStudentActiveInMgmtIds = Arrays::map($informatStudents, fn($is) => $is->id);
        $informatStudentActiveInMgmtIds = Arrays::filter($informatStudentActiveInMgmtIds, function ($isaimi) use ($informatRegistrationRepo, $informatRegistrationClassRepo) {
            $currentRegistration = $informatRegistrationRepo->getCurrentByInformatStudentId($isaimi);
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => Strings::equal($cr->status, 0));
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => Clock::now()->isAfterOrEqualTo(Clock::at($cr->start)));
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => is_null($cr->end));
            $currentRegistration = array_reverse(Arrays::orderBy($currentRegistration, "start"));
            $currentRegistration = Arrays::firstOrNull($currentRegistration);

            if (!$currentRegistration) return false;

            $currentRegistrationClass = $informatRegistrationClassRepo->getCurrentByInformatRegistrationId($currentRegistration->id);
            // $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)));
            // $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => is_null($crc->end));
            $currentRegistrationClass = array_reverse(Arrays::orderBy($currentRegistrationClass, 'start'));
            $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

            if (!$currentRegistrationClass) return false;
            return true;
        });
        $m365CurrentStudentIds = Arrays::map($currentStudents, fn($cs) => $cs->getEmployeeId());
        $notInEntraStudentIds = Arrays::filter($informatStudentActiveInMgmtIds, fn($isaimi) => !Arrays::filter($m365CurrentStudentIds, fn($csi) => Strings::equal($csi, $isaimi) || Strings::equal($csi, "L{$isaimi}")));
        $notInEntraStudents = Arrays::map($notInEntraStudentIds, fn($niesi) => Arrays::firstOrNull(Arrays::filter($informatStudents, fn($is) => Strings::equal($is->informatId, $niesi))));
        $notInEntraStudents = Arrays::filter($notInEntraStudents, fn($n) => !is_null($n));

        // Students disabled in Entra ID and but active in management - ENABLE
        $informatStudentActiveInMgmtIds = Arrays::map($informatStudents, fn($is) => $is->id);
        $informatStudentActiveInMgmtIds = Arrays::filter($informatStudentActiveInMgmtIds, function ($isaimi) use ($informatRegistrationRepo, $informatRegistrationClassRepo) {
            $currentRegistration = $informatRegistrationRepo->getCurrentByInformatStudentId($isaimi);
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => Strings::equal($cr->status, 0));
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => Clock::now()->isAfterOrEqualTo(Clock::at($cr->start)));
            // $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => is_null($cr->end));
            $currentRegistration = array_reverse(Arrays::orderBy($currentRegistration, "start"));
            $currentRegistration = Arrays::firstOrNull($currentRegistration);

            if (!$currentRegistration) return false;

            $currentRegistrationClass = $informatRegistrationClassRepo->getCurrentByInformatRegistrationId($currentRegistration->id);
            // $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)));
            // $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => is_null($crc->end));
            $currentRegistrationClass = array_reverse(Arrays::orderBy($currentRegistrationClass, 'start'));
            $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

            if (!$currentRegistrationClass) return false;
            return true;
        });
        $m365CurrentDisabledStudentIds = Arrays::map(Arrays::filter($currentStudents, fn($cs) => !$cs->getAccountEnabled()), fn($cs) => $cs->getEmployeeId());
        $activeButInactiveIds = Arrays::filter($informatStudentActiveInMgmtIds, fn($isaimi) => Arrays::filter($m365CurrentDisabledStudentIds, fn($mcdsi) => Strings::equal($mcdsi, $isaimi) || Strings::equal($mcdsi, "L{$isaimi}")));
        $activeButInactiveStudents = Arrays::map($activeButInactiveIds, fn($aii) => Arrays::firstOrNull(Arrays::filter($informatStudents, fn($is) => Strings::equal($is->informatId, $aii))));
        $activeButInactiveStudents = Arrays::map($activeButInactiveStudents, fn($i) => !is_null($i));

        // Students active in Entra ID and inactive in mgmt - DISABLE
        $m365CurrentEnabledStudentsIds = Arrays::map(Arrays::filter($currentStudents, fn($cs) => $cs->getAccountEnabled()), fn($ce) => $ce->getEmployeeId());
        $informatStudentInactiveInMgmtIds = Arrays::map($informatStudents, fn($is) => $is->id);
        $informatStudentInactiveInMgmtIds = Arrays::filter($informatStudentInactiveInMgmtIds, function ($isiimi) use ($informatRegistrationRepo, $informatRegistrationClassRepo) {
            $lastRegistration = $informatRegistrationRepo->getByInformatStudentId($isiimi);
            $lastRegistration = Arrays::filter($lastRegistration, fn($lr) => Strings::equal($lr->status, 0));
            $lastRegistration = array_reverse(Arrays::orderBy($lastRegistration, "start"));
            $lastRegistration = Arrays::firstOrNull($lastRegistration);
            return (!is_null($lastRegistration->end) && Clock::now()->isAfter(Clock::at($lastRegistration->end)));
        });
        $inactiveButActiveIds = Arrays::filter($informatStudentInactiveInMgmtIds, fn($isiimi) => Arrays::filter($m365CurrentEnabledStudentsIds, fn($mcesi) => Strings::equal($mcesi, $isiimi) || Strings::equal($mcesi, "L{$isiimi}")));
        $inactiveButActiveStudents = Arrays::map($inactiveButActiveIds, fn($iai) => Arrays::firstOrNull(Arrays::filter($informatStudents, fn($is) => Strings::equal($is->informatId, $iai))));
        $inactiveButActiveStudents = Arrays::filter($inactiveButActiveStudents, fn($i) => !is_null($i));

        foreach ($inactiveButActiveStudents as $inactiveButActiveStudent) {
            $sync = $syncRepo->getByEmployeeId($inactiveButActiveStudent->informatId) ?? new ObjectSync;
            $sync->type = "S";
            $sync->employeeId = $inactiveButActiveStudent->informatId;
            $sync->givenName = null;
            $sync->surname = null;
            $sync->displayName = null;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->companyName = null;
            $sync->department = null;
            $sync->jobTitle = null;
            $sync->memberOf = null;
            $sync->otherAttributes = null;
            $sync->password = null;
            $sync->ou = null;
            $sync->action = "D";

            $syncRepo->set($sync);
        }

        // Students in Entra ID and active - UPDATE
        foreach ($informatStudents as $informatStudent) {
            if (Arrays::contains(Arrays::map($notInEntraStudents, fn($e) => $e->informatId), $informatStudent->informatId)) continue;
            if (Arrays::contains(Arrays::map($activeButInactiveStudents, fn($e) => $e->informatId), $informatStudent->informatId)) continue;
            if (Arrays::contains(Arrays::map($inactiveButActiveStudents, fn($e) => $e->informatId), $informatStudent->informatId)) continue;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentStudents, fn($ce) => Strings::equal($ce->getEmployeeId(), $informatStudent->informatId) || Strings::equal($ce->getEmployeeId(), "L{$informatStudent->informatId}")));
            if (!$m365User) continue;

            $memberOfM365 = $m365UserRepo->getMemberOf($m365User->getId(), ['onPremisesDomainName', 'displayName']);
            $memberOfM365 = Arrays::filter($memberOfM365, fn($m) => $m->getOnPremisesDomainName() !== null);
            $memberOfM365 = Arrays::map($memberOfM365, fn($m) => $m->getDisplayName());

            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $informatStudent->firstName, $informatStudent->name);
            $CompanyName = $_settings['default']['companyName']["student"];
            $OU = $_settings['default']['ou']['student'];
            $MemberOf = [];

            $currentRegistration = $informatRegistrationRepo->getCurrentByInformatStudentId($informatStudent->id);
            $currentRegistration = array_reverse(Arrays::orderBy($currentRegistration, "start"));
            $currentRegistration = Arrays::firstOrNull($currentRegistration);

            $currentRegistrationClass = $informatRegistrationClassRepo->getCurrentByInformatRegistrationId($currentRegistration->id);
            $currentRegistrationClass = array_reverse(Arrays::orderBy($currentRegistrationClass, 'start'));
            $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

            $school = Arrays::first($schoolRepo->get(Arrays::first($instituteRepo->get($currentRegistration->schoolInstituteId))->schoolId));
            $class = Arrays::first($informatClassgroupRepo->get($currentRegistrationClass->informatClassGroupId));

            $CompanyName = str_replace("{{school:name}}", $school->name, $CompanyName);
            $OU = str_replace("{{school:adOuPart}}", $school->adOuPart, $OU);

            foreach ($_settings['default']['memberOf']['student'] as $mof) {
                if (is_null($school->adSecGroupPart)) continue;
                $mof = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof);
                if (!Arrays::contains($memberOfM365, $mof)) $MemberOf[] = $mof;
            }
            $MemberOf = array_unique($MemberOf);
            $m365OU = explode(",", $m365User->getOnPremisesDistinguishedName());
            array_shift($m365OU);
            $m365OU = implode(",", $m365OU);

            $sync = $syncRepo->getByEmployeeId($informatStudent->informatId) ?? new ObjectSync;
            $sync->type = "S";
            $sync->givenName = Strings::equal($informatStudent->firstName, $m365User->getGivenName()) ? null : $informatStudent->firstName;
            $sync->surname = Strings::equal($informatStudent->name, $m365User->getSurname()) ? null : $informatStudent->name;
            $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
            $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
            $sync->department = Strings::equal($class->code, $m365User->getDepartment()) ? null : ($class->code ?: null);
            $sync->ou = Strings::equal($OU, $m365OU) ? null : $OU;
            $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
            $sync->employeeId = $informatStudent->informatId;
            $sync->jobTitle = null;
            $sync->otherAttributes = null;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->password = null;
            $sync->action = "U";

            if ($sync->noUpdate()) $sync->action = null;
            $syncRepo->set($sync);
        }
    }
}
