<?php

namespace JAMF\Object;

use JAMF\Interface\CustomObject;

class Device extends CustomObject
{
	protected $objectAttributes = [
		'UDID',
		'locationId',
		'serialNumber',
		'assetTag',
		'inTrash',
		'class',
		'model',
		'os',
		'name',
		'owner',
		'isManaged',
		'isSupervised',
		'enrollType',
		'depProfile',
		'groups',
		'batteryLevel',
		'totalCapacity',
		'availableCapacity',
		'iCloudBackupEnabled',
		'iCloudBackupLatest',
		'iTunesStoreLoggedIn',
		'region',
		'notes',
		'lastCheckin',
		'modified',
		'networkInformation'
	];
}
