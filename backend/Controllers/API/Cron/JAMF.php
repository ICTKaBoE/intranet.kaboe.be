<?php

namespace Controllers\API\Cron;

use Ouzo\Utilities\Arrays;
use JAMF\Repository\Device;
use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Repository\Navigation;
use Database\Repository\Management\IPad;
use Database\Object\Management\IPad as ManagementIPad;
use Ouzo\Utilities\Clock;

abstract class JAMF
{
    public static function ImportIPads()
    {
        $start = Clock::now();
        $schools = (new School)->get();
        $items = (new Device)->get();
        $ipadRepo = new IPad;

        foreach ($items as $device) {
            $ipad = $ipadRepo->getByJamfId($device->UDID) ?? (new ManagementIPad);
            $ipad->schoolId = Arrays::firstOrNull(Arrays::filter($schools, fn($s) => Strings::containsIgnoreCase($device->name, $s->jamfIpadPrefix)))->id ?? 0;
            $ipad->jamfId = $device->UDID;
            $ipad->serialnumber = $device->serialNumber;
            $ipad->model = $device->model['name'];
            $ipad->osPrefix = $device->os['prefix'];
            $ipad->osVersion = $device->os['version'];
            $ipad->name = $device->name;
            $ipad->batteryLevel = $device->batteryLevel * 100;
            $ipad->totalCapacity = $device->totalCapacity;
            $ipad->availableCapacity = $device->availableCapacity;
            $ipad->lastCheckin = $device->lastCheckin;

            $ipadRepo->set($ipad);
        }

        $end = Clock::now();

        $settings = [];
        $settings["ipad"]["lastSyncTime"] = $start->format("d/m/Y H:i:s") . ' - ' . $end->format('d/m/Y H:i:s') . ' (' . (strtotime($end->format("Y-m-d H:i:s")) - strtotime($start->format("Y-m-d H:i:s"))) . ' seconden)';

        $repo = new Navigation;
        $item = Arrays::first($repo->getByParentIdAndLink(0, 'management'));
        $item->settings = json_encode(array_replace_recursive($item->settings, $settings));

        $repo->set($item, ['settings']);

        return true;
    }
}
