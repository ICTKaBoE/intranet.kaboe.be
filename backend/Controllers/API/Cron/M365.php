<?php

namespace Controllers\API\Cron;

use Security\GUID;
use Helpers\General;
use M365\Repository\Team;
use M365\Repository\User;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use function Ramsey\Uuid\v1;
use Database\Repository\School;
use Database\Repository\Setting;
use Database\Repository\Navigation;
use Database\Repository\SecurityGroup;
use Database\Object\User as ObjectUser;
use Database\Repository\SecurityGroupUser;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Management\Computer;
use Database\Repository\User as RepositoryUser;
use M365\Repository\Computer as RepositoryComputer;

use Database\Object\Management\Computer as ManagementComputer;
use Database\Object\Management\ComputerUsageLogOn as ManagementComputerUsageLogOn;
use Database\Object\SecurityGroupUser as ObjectSecurityGroupUser;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Management\ComputerUsageLogOn;
use M365\Repository\AuditLog;
use M365\Repository\Group;
use Security\Input;

abstract class M365
{
    static public function ImportUsers()
    {
        $settings = new Setting;
        $securityGroupRepo = new SecurityGroup;
        $sguRepo = new SecurityGroupUser;
        $userRepo = new RepositoryUser;

        $securityGroups = Arrays::filter($securityGroupRepo->get(), fn($s) => Strings::isNotBlank($s->m365GroupId));

        foreach ($securityGroups as $sg) {
            $sguRepo->delete(["securityGroupId" => $sg->id]);
            $members = (new User)->getGroupMembersByGroupId($sg->m365GroupId, ['id', 'mail', 'employeeId', 'surname', 'givenName']);

            foreach ($members as $member) {
                if (Arrays::contains(["#microsoft.graph.group"], $member->getOdataType())) continue;
                $user = $userRepo->getByEntraId($member->getId()) ?? $userRepo->getByInformatEmployeeId($member->getEmployeeId()) ?? Arrays::firstOrNull($userRepo->getByUsername($member->getMail())) ?? null;

                if ($user) {
                    $user->entraId = $member->getId();
                    $userRepo->set($user);

                    $sgu = new ObjectSecurityGroupUser;
                    $sgu->securityGroupId = $sg->id;
                    $sgu->userId = $user->id;

                    $sguRepo->set($sgu);
                }
            }
        }

        return true;
    }

    static public function ImportComputers()
    {
        $start = Clock::now();
        $settings = new Setting;
        $schools = (new School)->get();
        $managementComputerRepo = new Computer;

        $members = (new RepositoryComputer)->getGroupMembersByGroupId(Arrays::first($settings->get("m365.asset.groupId"))->value);

        foreach ($members as $member) {
            if (Arrays::contains(["#microsoft.graph.group"], $member->getOdataType())) continue;

            $computer = $managementComputerRepo->getByEntraId($member->getId()) ?? $managementComputerRepo->getByName($member->getDisplayName()) ?? new ManagementComputer;
            $computer->entraId = $member->getId();
            $computer->schoolId = Arrays::firstOrNull(Arrays::filter($schools, fn($s) => Strings::containsIgnoreCase(Arrays::first(Arrays::filter($member->getPhysicalIds(), fn($p) => Strings::contains($p, 'OrderId'))), $s->intuneOrderIdPrefix)))->id ?? 0;
            $computer->type = (Strings::contains(Arrays::first(Arrays::filter($member->getPhysicalIds(), fn($p) => Strings::contains($p, 'OrderId'))), "DESKTOP") ? "D" : "L");
            $computer->name = $member->getDisplayName();
            $computer->orderId = Arrays::last(explode(":", Arrays::first(Arrays::filter($member->getPhysicalIds(), fn($p) => Strings::contains($p, 'OrderId')))));
            $computer->enrollmentProfileName = $member->getEnrollmentProfileName();
            $computer->osType = $member->getOperatingSystem();
            $computer->osVersion = $member->getOperatingSystemVersion();
            $computer->manufacturer = $member->getManufacturer();
            $computer->model = $member->getModel();

            $managementComputerRepo->set($computer);
        }

        $end = Clock::now();

        $settings = [];
        $settings["computer"]["lastSyncTime"] = $start->format("d/m/Y H:i:s") . ' - ' . $end->format('d/m/Y H:i:s') . ' (' . (strtotime($end->format("Y-m-d H:i:s")) - strtotime($start->format("Y-m-d H:i:s"))) . ' seconden)';

        $repo = new Navigation;
        $item = Arrays::first($repo->getByParentIdAndLink(0, 'management'));
        $item->settings = array_replace_recursive($item->settings, $settings);

        $repo->set($item, ['settings']);

        return true;
    }

    static public function ImportSignInTimes()
    {
        $computerRepo = new Computer;
        $computerLogonRepo = new ComputerUsageLogOn;
        $signIns = (new AuditLog)->getWindowsSignIn(7, ["deviceDetail", "createdDateTime", "userPrincipalName", "userId", "ipAddress"]);

        foreach ($signIns as $signIn) {
            $computer = $computerRepo->getByName($signIn->getDeviceDetail()->getDisplayName());
            if (!$computer) continue;

            $logon = $computerLogonRepo->getByComputerIdAndLogon($computer->id, $signIn->getCreatedDateTime()->format("Y-m-d H:i:s")) ?? (new ManagementComputerUsageLogOn);
            $logon->computerId = $computer->id;
            $logon->username = $signIn->getUserPrincipalName() ?: null;
            $logon->logon = $signIn->getCreatedDateTime()->format("Y-m-d H:i:s");
            $logon->logoff = null;

            $computerLogonRepo->set($logon);
        }

        return true;
    }

    static public function SyncClassTeams()
    {
        $navRepo = new Navigation;
        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_minDepartmentCodes = $_settings['minimum']['departmentCode'];
        $_minGrade = General::convert($_settings['minimum']['grade'], 'int');
        $_minYear = General::convert($_settings['minimum']['year'], 'int');
        $_classOwner = $_settings['default']['teams']['owner'];
        $_templateId = $_settings['default']['teams']['template'];
        $_rule = $_settings['default']['teams']['rule']['class'];
        $_name = $_settings['default']['teams']['name']['class'];

        $teamsRepo = new Team;
        $groupsRepo = new Group;
        $employeeRepo = new Employee;
        $m365UserRepo = new User;
        $employeeOwnfieldRepo = new EmployeeOwnfield;
        $classes = (new ClassGroup)->getBySchoolyear(INFORMAT_CURRENT_SCHOOLYEAR);

        $defaultOwner = Input::check($_classOwner, Input::INPUT_TYPE_EMAIL) ? $m365UserRepo->getByEmail($_classOwner)->getId() : $_classOwner;

        try {
            foreach ($classes as $class) {
                if ($class->type == "S") continue;
                if (!Arrays::contains($_minDepartmentCodes, $class->departmentCode)) continue;
                if ($class->grade < $_minGrade) continue;
                if ($class->year < $_minYear) continue;

                $name = $_name;
                $rule = $_rule;

                foreach ($class->toArray(true) as $k => $v) {
                    $name = str_replace("{{class:{$k}}}", $v, $name);
                    $rule = str_replace("{{class:{$k}}}", $v, $rule);
                }

                $exTeam = $teamsRepo->getByName($name, ["id", "description"]);
                $exGroup = $groupsRepo->getByName($name, ['id', 'groupTypes']);

                $ofs = $employeeOwnfieldRepo->getBySectionAndName(2, "{$class->linked->schoolInstitute->linked->school->name} - Klas(sen)");
                $ofs = Arrays::filter($ofs, fn($of) => Arrays::contains(Arrays::map(explode(",", $of->value), fn($c) => trim($c)), $class->code));

                $classOwners = Arrays::map($ofs, function ($of) use ($employeeRepo, $m365UserRepo) {
                    $employee = Arrays::firstOrNull($employeeRepo->get($of->informatEmployeeId));
                    if (!$employee) return false;

                    $m365User = $m365UserRepo->getByEmployeeId($employee->informatId, ['id']);
                    if (!$m365User) return false;

                    return $m365User->getId();
                });
                $classOwners[] = $defaultOwner;
                $classOwners = array_values($classOwners);

                if (count($exTeam) && count($exGroup)) {
                    $exGroup = Arrays::firstOrNull($exGroup);

                    if (!Arrays::contains($exGroup->getGroupTypes(), "DynamicMembership")) {
                        $groupsRepo->convertToDynamicMembership($exGroup->getId(), $rule);
                    }

                    $groupsRepo->setOwners($exGroup->getId(), $classOwners);
                } else if (count($exTeam) == 0) {
                    $teamsRepo->create($name, $_templateId, $classOwners);

                    while (count($exGroup) == 0) {
                        $exGroup = $groupsRepo->getByName($name, ['id', 'groupTypes']);
                    }

                    $exGroup = Arrays::firstOrNull($exGroup);

                    if (!Arrays::contains($exGroup->getGroupTypes(), "DynamicMembership")) {
                        $groupsRepo->convertToDynamicMembership($exGroup->getId(), $rule);
                    }
                }
            }
        } catch (\Exception $e) {
            die(var_dump($e));
        }

        return true;
    }

    static public function SyncSchoolTeams()
    {
        $navRepo = new Navigation;
        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_rule = $_settings['default']['teams']['rule']['school'];
        $_name = $_settings['default']['teams']['name']['school'];

        $teamsRepo = new Team;
        $groupsRepo = new Group;

        $schools = (new School)->get();

        foreach ($schools as $school) {
            $name = $_name;
            $rule = $_rule;

            foreach ($school->toArray(true) as $k => $v) {
                $name = str_replace("{{school:{$k}}}", $v, $name);
                $rule = str_replace("{{school:{$k}}}", $v, $rule);
            }

            $team = $teamsRepo->getByName($name, ['id', 'displayName', 'description']);
            $team = Arrays::firstOrNull($team);
            if (!$team) continue;

            $group = $groupsRepo->getByName($name, ['id', 'groupTypes']);
            $group = Arrays::firstOrNull($group);
            if (!$group) continue;

            if ($school->dynamicTeam && !Arrays::contains($group->getGroupTypes(), "DynamicMembership")) {
                $groupsRepo->convertToDynamicMembership($group->getId(), $rule);
            }

            $channels = $teamsRepo->getChannels($team->getId(), ['id', 'displayName', 'description']);
            $channels = Arrays::filter($channels, fn($c) => Strings::contains($c->getDescription(), "sgGroupId"));

            foreach ($channels as $channel) {
                $sgGroupId = Arrays::last(explode(":", $channel->getDescription()));
                $sgGroupMembers = $groupsRepo->getMembers($sgGroupId)->getValue();
                $sgGroupMembers = Arrays::map($sgGroupMembers, fn($sgm) => $sgm->getId());

                $teamsRepo->setChannelMembers($team->getId(), $channel->getId(), $sgGroupMembers);
            }
        }
    }
}
