<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Security\Input;
use Helpers\General;
use M365\Repository\Team;
use M365\Repository\User;
use Ouzo\Utilities\Clock;
use M365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use M365\Repository\AuditLog;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\School\School;
use Database\Repository\Setting\Setting;
use Database\Object\Mail\Mail as MailMail;
use Database\Object\User\User as UserUser;
use Database\Repository\Informat\Employee;
use Database\Repository\General\Schoolyear;
use Database\Repository\Security\GroupUser;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Management\Computer;
use Database\Repository\Navigation\Navigation;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Informat\EmployeeOwnfield;
use M365\Repository\Computer as RepositoryComputer;
use Database\Repository\User\User as RepositoryUser;
use Database\Repository\Management\ComputerUsageLogOn;
use Database\Repository\Security\Group as SecurityGroup;
use Database\Object\Management\Computer as ManagementComputer;
use Database\Repository\Navigation\Setting as NavigationSetting;
use Database\Object\Security\GroupUser as ObjectSecurityGroupUser;
use Database\Object\Management\ComputerUsageLogOn as ManagementComputerUsageLogOn;

abstract class M365
{
    static public function ImportUsers()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import M365 Users...");

        $securityGroupRepo = new SecurityGroup;
        $sguRepo = new GroupUser;
        $userRepo = new RepositoryUser;

        $securityGroups = Arrays::filter($securityGroupRepo->get(), fn($s) => Strings::isNotBlank($s->m365GroupId));

        foreach ($securityGroups as $sg) {
            $sguRepo->delete(["securityGroupId" => $sg->id]);
            $members = (new User)->getGroupMembersByGroupId($sg->m365GroupId, ['id', 'mail', 'employeeId', 'surname', 'givenName', 'companyName']);

            foreach ($members as $member) {
                if (Arrays::contains(["#microsoft.graph.group"], $member->getOdataType())) continue;

                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "User: {$member->getEmployeeId()} - {$member->getSurname()} {$member->getGivenName()}");
                $user = $userRepo->getByEntraId($member->getId()) ?? (new UserUser);

                $user->entraId = $member->getId();
                $user->entraCompany = $member->getCompanyName();
                $user->informatEmployeeId = $member->getEmployeeId();
                $user->mainSchoolId = 0;
                $user->username = $member->getMail();
                $user->name = $member->getSurname();
                $user->firstName = $member->getGivenName();
                $nId = $userRepo->set($user);
                if (!$user->id) $user = $userRepo->getById($nId);

                if ($sguRepo->getBySecurityGroupIdAndUserId($sg->id, $user->id)) continue;
                $sgu = new ObjectSecurityGroupUser;
                $sgu->securityGroupId = $sg->id;
                $sgu->userId = $user->id;

                $sguRepo->set($sgu);
            }
        }

        return true;
    }

    // /usr/bin/php /data/sites/web/kaboebe/www/cron.php part=m365 function=importComputers
    static public function ImportComputers()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to import M365 Computers...");

        $start = Clock::now();
        $settings = new Setting;
        $schools = (new School)->get();
        $managementComputerRepo = new Computer;

        $members = (new RepositoryComputer)->getGroupMembersByGroupId(Arrays::first($settings->get("m365.asset.groupId"))->value);

        foreach ($members as $member) {
            if (Arrays::contains(["#microsoft.graph.group"], $member->getOdataType())) continue;
            $orderId = Arrays::last(explode(":", Arrays::first(Arrays::filter($member->getPhysicalIds(), fn($p) => Strings::contains($p, 'OrderId')))));
            $school = Arrays::firstOrNull(Arrays::filter($schools, fn($s) => Strings::isNotBlank(Strings::trimToNull($s->intuneOrderIdPrefix)) && Strings::containsIgnoreCase($orderId, $s->intuneOrderIdPrefix)));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$member->getDisplayName()} - {$orderId} - {$school->name}");

            $computer = $managementComputerRepo->getByEntraId($member->getId()) ?? $managementComputerRepo->getByName($member->getDisplayName()) ?? new ManagementComputer;
            $computer->entraId = $member->getId();
            $computer->type = (Strings::contains(Arrays::first(Arrays::filter($member->getPhysicalIds(), fn($p) => Strings::contains($p, 'OrderId'))), "DESKTOP") ? "D" : "L");
            $computer->name = $member->getDisplayName();
            $computer->orderId = $orderId;
            $computer->schoolId = $school->id ?: 0;
            $computer->enrollmentProfileName = $member->getEnrollmentProfileName();
            $computer->osType = $member->getOperatingSystem();
            $computer->osVersion = $member->getOperatingSystemVersion();
            $computer->manufacturer = $member->getManufacturer();
            $computer->model = $member->getModel();

            $managementComputerRepo->set($computer);
        }

        $end = Clock::now();

        $repo = new NavigationSetting;
        $item = $repo->getByNavigationIdAndKey((new Navigation)->getByLinkAndType('management', "M")->id, "computer.lastSyncTime");
        $item->value = $start->format("d/m/Y H:i:s") . ' - ' . $end->format('d/m/Y H:i:s') . ' (' . (strtotime($end->format("Y-m-d H:i:s")) - strtotime($start->format("Y-m-d H:i:s"))) . ' seconden)';
        $repo->set($item);

        return true;
    }

    static public function ImportSignInTimes()
    {
        $computerRepo = new Computer;
        $computerLogonRepo = new ComputerUsageLogOn;
        $signIns = (new AuditLog)->getWindowsSignIn(30, ["deviceDetail", "createdDateTime", "userPrincipalName", "userId", "ipAddress"]);

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
        $settingRepo = new NavigationSetting;
        $navigation = (new Navigation)->getByLinkAndType("sync", "M");
        $_minDepartmentCodes = explode(PHP_EOL, $settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.departmentCode")->value);
        $_minGrade = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.grade")->value, 'int');
        $_minYear = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.year")->value, 'int');
        $_classOwner = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.owner")->value;
        $_templateId = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.template")->value;
        $_rule = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.rule.class")->value;
        $_name = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.name.class")->value;

        $teamsRepo = new Team;
        $groupsRepo = new Group;
        $employeeRepo = new Employee;
        $m365UserRepo = new User;
        $employeeOwnfieldRepo = new EmployeeOwnfield;
        $classes = (new ClassGroup)->getBySchoolyear(_CURRENT_SCHOOLYEAR_);

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
        $settingRepo = new NavigationSetting;
        $navigation = (new Navigation)->getByLinkAndType("sync", "M");
        $_rule = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.rule.school")->value;
        $_name = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.teams.name.school")->value;

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

        return true;
    }

    static public function WarnUserPasswordExpiration()
    {
        $settingRepo = new NavigationSetting;
        $navigation = (new Navigation)->getByLinkAndType("sync", "M");
        $_days = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.password.expiration.days")->value;
        $_startFrom = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.password.expiration.start")->value;
        $_subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.password.subject")->value;
        $_body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.password.body")->value;
        $employeeRepo = new Employee;

        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $groupId = Arrays::firstOrNull((new Setting)->get("m365.employee.groupId"))->value;
        $members = (new User)->getGroupMembersByGroupId($groupId, ['id', 'accountEnabled', 'mail', 'employeeId', 'lastPasswordChangeDateTime', 'onPremisesExtensionAttributes']);

        foreach ($members as $member) {
            if (Arrays::contains(["#microsoft.graph.group"], $member->getOdataType())) continue;
            if (!$member->getAccountEnabled()) continue;
            if (Strings::contains($member->getOnPremisesExtensionAttributes()->getExtensionAttribute2(), "passreset:false")) continue;

            $startWarnFrom = Clock::at(Clock::at($member->getLastPasswordChangeDateTime()->format("Y-m-d H:i:s"))->toDateTime()->modify("+{$_days}-{$_startFrom}")->format("Y-m-d H:i:s"));
            if (!Clock::now()->isAfterOrEqualTo($startWarnFrom)) continue;

            $passwordExpirationDate = Clock::at(Clock::at($member->getLastPasswordChangeDateTime()->format("Y-m-d H:i:s"))->toDateTime()->modify("+{$_days}")->format("Y-m-d H:i:s"));
            $difference = Clock::now()->toDateTime()->diff($passwordExpirationDate->toDateTime());

            $employee = $employeeRepo->getByInformatId($member->getEmployeeId());
            if (!$employee) continue;

            $subject = str_replace("{{days}}", ($difference->invert ? "-" : "") . $difference->days, $_subject);
            $subject = str_replace("{{expirationDate}}", $passwordExpirationDate->format("d/m/Y"), $subject);
            $subject = str_replace("{{passwordLastSetDate}}", $member->getLastPasswordChangeDateTime()->format("d/m/Y H:i:s"), $subject);

            $body = str_replace("{{days}}", $difference->days, $_body);
            $body = str_replace("{{expirationDate}}", $passwordExpirationDate->format("d/m/Y"), $body);
            $body = str_replace("{{passwordLastSetDate}}", $member->getLastPasswordChangeDateTime()->format("d/m/Y H:i:s"), $body);

            foreach ($employee->toArray(true) as $k => $v) {
                $subject = str_replace("{{employee:{$k}}}", $v, $subject);
                $body = str_replace("{{employee:{$k}}}", $v, $body);
            }

            $mail = new MailMail;
            $mail->subject = $subject;
            $mail->body = $body;

            $mId = $mailRepo->set($mail);

            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->email = $member->getMail();
            $receiver->name = $employee->formatted->fullNameReversed;
            $mailReceiverRepo->set($receiver);
        }

        return true;
    }
}
