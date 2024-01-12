<?php

namespace Cron;

use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Repository\InformatStaff;
use Database\Repository\InformatStaffFreeField;
use Database\Object\UserAddress as ObjectUserAddress;
use Database\Object\UserProfile as ObjectUserProfile;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserProfile;

abstract class CronLocal
{
	public static function UserSync()
	{
		self::Address();
		self::Profile();
	}

	private static function Address()
	{
		$localUserRepo = new LocalUser;
		$localUserAddressRepo = new UserAddress;
		$informatStaffRepo = new InformatStaff;

		try {
			$localUserAddressRepo->db->beginTransaction();

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

			$localUserAddressRepo->db->commit();
		} catch (\Exception $e) {
			$localUserAddressRepo->db->rollback();
		}
	}

	private static function Profile()
	{
		$localUserRepo = new LocalUser;
		$informatStaffRepo = new InformatStaff;
		$informatStaffFreeFieldRepo = new InformatStaffFreeField;
		$userProfileRepo = new UserProfile;
		$schoolRepo = new School;

		try {
			$userProfileRepo->db->beginTransaction();

			foreach ($localUserRepo->get() as $localUser) {
				$informatStaff = $informatStaffRepo->getBySchoolEmail($localUser->username);
				if (is_null($informatStaff)) continue;

				$informatStaffFreeField = $informatStaffFreeFieldRepo->getByStaffIdAndDescription($informatStaff->id, "pedagogische school 1")->value;
				$userProfile = $userProfileRepo->getByUserId($localUser->id) ?? (new ObjectUserProfile);
				$userProfile->userId = $localUser->id;
				$userProfile->mainSchoolId = $schoolRepo->getByName($informatStaffFreeField)->id;
				$userProfile->bankAccount = $informatStaff->bankAccount;
				$userProfileRepo->set($userProfile);
			}

			$userProfileRepo->db->commit();
		} catch (\Exception $e) {
			$userProfileRepo->db->rollback();
		}
	}
}
