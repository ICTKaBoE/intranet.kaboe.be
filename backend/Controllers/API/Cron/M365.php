<?php

namespace Controllers\API\Cron;

use Security\GUID;
use M365\Repository\User;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Repository\Setting;
use Database\Repository\Navigation;
use Database\Object\User as ObjectUser;
use Database\Repository\Management\Computer;
use Database\Repository\User as RepositoryUser;
use M365\Repository\Computer as RepositoryComputer;
use Database\Object\Management\Computer as ManagementComputer;
use Database\Object\SecurityGroupUser as ObjectSecurityGroupUser;
use Database\Repository\SecurityGroup;
use Database\Repository\SecurityGroupUser;

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

    static public function ExpiredPasswords() {}
}
